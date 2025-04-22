<?php

namespace App\Services;

use App\Models\Tag;
use League\Csv\Reader;
use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\CustomAttribute;
use App\Models\ContactIdentifier;
use Illuminate\Support\Facades\DB;
use App\Models\ContactCustomAttribute;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResources;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactService extends BaseService
{
    /**
     * Show contacts.
     *
     * @param string|null $organizationId
     * @return ContactResources|array
     */
    public function showContacts(?string $organizationId = null): ContactResources|array
    {
        $query = Contact::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create contact.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createContact(string $organizationId, array $data): array
    {
        return DB::transaction(function () use ($organizationId, $data) {
            $data['organization_id'] = $organizationId;
            $contact = Contact::create([
                'organization_id' => $data['organization_id'],
                'favorite_user_id' => $data['favorite_user_id'] ?? null,
            ]);

            if (isset($data['identifiers'])) {
                $identifiers = array_map(function ($identifier) use ($contact) {
                    return [
                        'id' => Str::uuid(),
                        'contact_id' => $contact->id,
                        'type' => $identifier['type'],
                        'value' => $identifier['value'],
                        'is_primary' => $identifier['is_primary'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $data['identifiers']);
                ContactIdentifier::insert($identifiers);
            }

            if (isset($data['custom_attributes'])) {
                $customAttributes = CustomAttribute::where('organization_id', $organizationId)
                    ->whereIn('id', array_column($data['custom_attributes'], 'custom_attribute_id'))
                    ->get()
                    ->keyBy('id');

                $attributes = array_map(function ($attr) use ($contact, $customAttributes) {
                    $customAttr = $customAttributes[$attr['custom_attribute_id']] ?? null;
                    return [
                        'id' => Str::uuid(),
                        'contact_id' => $contact->id,
                        'custom_attribute_id' => $attr['custom_attribute_id'],
                        'name' => $customAttr ? $customAttr->name : $attr['name'],
                        'type' => $customAttr ? $customAttr->type : $attr['type'],
                        'value' => $attr['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $data['custom_attributes']);
                ContactCustomAttribute::insert($attributes);
            }

            if (isset($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        [
                            'name' => $tagName,
                            'organization_id' => $data['organization_id']
                        ],
                        [
                            'name' => $tagName,
                            'organization_id' => $data['organization_id']
                        ]
                    );
                    $tagIds[] = $tag->id;
                }
                $contact->tags()->sync($tagIds);
            }

            return $this->showCreatedResource($contact);
        });
    }

    /**
     * Import contacts from CSV.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function importContacts(string $organizationId, array $data): array
    {
        return DB::transaction(function () use ($organizationId, $data) {

            // Load custom attributes for the organization
            $customAttributes = CustomAttribute::where('organization_id', $organizationId)
                ->select('id', 'name', 'type')
                ->get()
                ->keyBy('name')
                ->toArray();

            // Validate that required custom attributes exist in the organization
            if (!isset($customAttributes['name'])) {
                throw ValidationException::withMessages([
                    'csv_file' => "The 'name' custom attribute must be defined for the organization before importing contacts.",
                ]);
            }

            // Create or fetch tags
            $tagIds = [];
            if (isset($data['tags'])) {
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        [
                            'name' => $tagName,
                            'organization_id' => $organizationId
                        ],
                        [
                            'name' => $tagName,
                            'organization_id' => $organizationId
                        ]
                    );
                    $tagIds[] = $tag->id;
                }
            }

            // Parse CSV
            $csv = Reader::createFromPath($data['csv_file']->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            $headers = $csv->getHeader();
            $records = iterator_to_array($csv->getRecords());

            // Validate CSV headers: 'name' column must be present
            $normalizedHeaders = array_map('trim', array_map('strtolower', $headers));
            if (!in_array('name', $normalizedHeaders)) {
                throw ValidationException::withMessages([
                    'csv_file' => "The CSV file must contain a 'name' column.",
                ]);
            }

            $errors = [];
            $allIdentifiers = [];
            $contactDataList = [];

            // Step 1: Process CSV records and collect identifiers
            foreach ($records as $index => $record) {

                $rowNumber = $index + 2; // Account for header row and 1-based indexing
                $contactData = [
                    'identifiers' => [],
                    'custom_attributes' => [],
                    'organization_id' => $organizationId,
                    'tags' => $tagIds ? array_keys(Tag::whereIn('id', $tagIds)->pluck('name', 'id')->toArray()) : [],
                    'csv_index' => $index,
                    'row_number' => $rowNumber,
                ];

                // Map CSV columns to contact data
                $nameValue = null;
                foreach ($record as $key => $value) {
                    $key = trim(strtolower($key));
                    $value = trim($value);

                    if ($key === 'phone' && !empty($value)) {
                        $phoneNumbers = explode(':', $value);
                        foreach ($phoneNumbers as $phoneIndex => $phoneNumber) {
                            $phoneNumber = trim($phoneNumber);
                            if (!empty($phoneNumber)) {
                                $contactData['identifiers'][] = [
                                    'type' => 'phone',
                                    'value' => $phoneNumber,
                                    'is_primary' => $phoneIndex === 0,
                                ];
                                $allIdentifiers[] = [
                                    'type' => 'phone',
                                    'value' => $phoneNumber,
                                    'index' => $index,
                                ];
                            }
                        }
                    } elseif ($key === 'email' && !empty($value)) {
                        $emailAddresses = explode(':', $value);
                        foreach ($emailAddresses as $emailIndex => $emailAddress) {
                            $emailAddress = trim($emailAddress);
                            if (!empty($emailAddress)) {
                                $contactData['identifiers'][] = [
                                    'type' => 'email',
                                    'value' => $emailAddress,
                                    'is_primary' => $emailIndex === 0,
                                ];
                                $allIdentifiers[] = [
                                    'type' => 'email',
                                    'value' => $emailAddress,
                                    'index' => $index,
                                ];
                            }
                        }
                    } elseif ($key === 'name') {
                        $nameValue = $value;
                        if (!empty($value)) {
                            $contactData['custom_attributes'][] = [
                                'custom_attribute_id' => $customAttributes['name']['id'],
                                'name' => 'name',
                                'type' => 'string',
                                'value' => $value,
                            ];
                        }
                    } elseif (isset($customAttributes[$key]) && !empty($value)) {
                        $contactData['custom_attributes'][] = [
                            'custom_attribute_id' => $customAttributes[$key]['id'],
                            'name' => $customAttributes[$key]['name'],
                            'type' => $customAttributes[$key]['type'],
                            'value' => $value,
                        ];
                    }
                }

                // Validate the contact data
                $validator = Validator::make($contactData, [
                    'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
                    'identifiers' => [
                        'required',
                        'array',
                        'min:1',
                        function ($attribute, $value, $fail) {
                            $hasPhone = collect($value)->contains('type', 'phone');
                            if (!$hasPhone) {
                                $fail("At least one phone number is required for each contact.");
                            }
                        },
                    ],
                    'identifiers.*.type' => ['required', 'in:phone,email,external_id'],
                    'identifiers.*.value' => [
                        'required',
                        'string',
                        'max:255',
                        function ($attribute, $value, $fail) use ($contactData) {
                            $index = explode('.', $attribute)[1];
                            $type = $contactData['identifiers'][$index]['type'];
                            if ($type === 'phone' && !preg_match('/^\+[1-9]\d{1,14}$/', $value)) {
                                $fail("The $attribute '$value' must be a valid phone number in E.164 format (e.g., +1234567890).");
                            } elseif ($type === 'email') {
                                $emailValidator = Validator::make(['email' => $value], ['email' => 'nullable|email:rfc']);
                                if ($emailValidator->fails()) {
                                    $fail("The $attribute '$value' must be a valid email address.");
                                }
                            }
                        },
                    ],
                    'identifiers.*.is_primary' => ['sometimes', 'boolean'],
                    'custom_attributes' => [
                        'required',
                        'array',
                        function ($attribute, $value, $fail) use ($customAttributes, $nameValue) {
                            $hasName = collect($value)->contains('custom_attribute_id', $customAttributes['name']['id'] ?? null);
                            if (!$hasName || empty($nameValue)) {
                                $fail("The 'name' custom attribute is required and must have a non-empty value for each contact.");
                            }
                        },
                    ],
                    'custom_attributes.*.custom_attribute_id' => [
                        'required',
                        'uuid',
                        Rule::exists('custom_attributes', 'id')->where('organization_id', $organizationId)
                    ],
                    'custom_attributes.*.value' => [
                        'required',
                        'string',
                        function ($attribute, $value, $fail) use ($contactData, $customAttributes) {
                            $index = explode('.', $attribute)[1];
                            $customAttributeId = $contactData['custom_attributes'][$index]['custom_attribute_id'];
                            $attribute = collect($customAttributes)->firstWhere('id', $customAttributeId);
                            if ($attribute && $attribute['type'] === 'url') {
                                $urlValidator = Validator::make(['url' => $value], ['url' => 'url']);
                                if ($urlValidator->fails()) {
                                    $fail("The $attribute[name] '$value' must be a valid URL (e.g., https://example.com).");
                                }
                            }
                        },
                    ],
                    'tags.*' => ['string', 'max:100'],
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'record' => $record,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }

                $contactDataList[] = $contactData;
            }

            $createdCount = 0;
            $updatedCount = 0;
            $importedCount = 0;
            $total = count($records);

            if($total) {

                // Step 2: Find existing contacts by identifiers in one query
                $existingIdentifiers = ContactIdentifier::whereIn('value', array_column($allIdentifiers, 'value'))
                    ->whereHas('contact', fn($query) => $query->where('organization_id', $organizationId))
                    ->with('contact')
                    ->get()
                    ->keyBy('value');

                // Step 3: Map identifiers to existing contacts and separate new/existing contacts
                $newContacts = [];
                $updateAttributes = [];
                $existingContactMap = []; // [csv_index => contact_id]

                foreach ($allIdentifiers as $identifier) {
                    $index = $identifier['index'];
                    $value = $identifier['value'];

                    if (isset($existingIdentifiers[$value])) {
                        $contactId = $existingIdentifiers[$value]->contact_id;
                        $existingContactMap[$index] = $contactId;
                    }
                }

                // Step 4: Separate new and existing contacts
                foreach ($contactDataList as $contactData) {
                    $index = $contactData['csv_index'];
                    if (isset($existingContactMap[$index])) {
                        // Existing contact: Prepare to update custom attributes
                        $contactId = $existingContactMap[$index];
                        $this->prepareCustomAttributesUpdate($contactId, $contactData, $updateAttributes);
                    } else {
                        // New contact: Prepare for creation
                        $newContacts[] = $contactData;
                    }
                }

                // Step 5: Batch insert new contacts
                $newContactIds = [];
                if (!empty($newContacts)) {
                    $newContactData = array_map(function ($contactData) {
                        return [
                            'id' => Str::uuid(),
                            'organization_id' => $contactData['organization_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }, $newContacts);

                    Contact::insert($newContactData);
                    $createdCount = count($newContactData);

                    // Map new contact IDs for identifiers and attributes
                    foreach ($newContacts as $index => $newContact) {
                        $newContactIds[$index] = $newContactData[$index]['id'];
                    }

                    // Step 6: Batch insert identifiers for new contacts
                    $newIdentifiers = [];
                    foreach ($newContacts as $index => $newContact) {
                        $contactId = $newContactIds[$index];
                        foreach ($newContact['identifiers'] as $identifier) {
                            $newIdentifiers[] = [
                                'id' => Str::uuid(),
                                'contact_id' => $contactId,
                                'type' => $identifier['type'],
                                'value' => $identifier['value'],
                                'is_primary' => $identifier['is_primary'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($newIdentifiers)) {
                        ContactIdentifier::insert($newIdentifiers);
                    }

                    // Step 7: Batch insert custom attributes for new contacts
                    $newAttributes = [];
                    foreach ($newContacts as $index => $newContact) {
                        $contactId = $newContactIds[$index];
                        $this->prepareCustomAttributesInsert($contactId, $newContact, $newAttributes);
                    }

                    if (!empty($newAttributes)) {
                        ContactCustomAttribute::upsert(
                            $newAttributes,
                            ['contact_id', 'custom_attribute_id'],
                            ['value', 'updated_at']
                        );
                    }

                    // Step 8: Batch assign tags to new contacts
                    if (!empty($tagIds)) {
                        $tagAssignments = [];
                        foreach ($newContactIds as $contactId) {
                            foreach ($tagIds as $tagId) {
                                $tagAssignments[] = [
                                    'contact_id' => $contactId,
                                    'tag_id' => $tagId,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        if (!empty($tagAssignments)) {
                            DB::table('contact_tag')->insert($tagAssignments);
                        }
                    }
                }

                // Step 9: Batch update custom attributes for existing contacts
                if (!empty($updateAttributes)) {
                    ContactCustomAttribute::upsert(
                        $updateAttributes,
                        ['contact_id', 'custom_attribute_id'],
                        ['value', 'updated_at']
                    );
                    $updatedCount = count(array_unique(array_column($updateAttributes, 'contact_id')));
                }

                // Step 10: Batch assign tags to existing contacts
                if (!empty($tagIds) && !empty($existingContactMap)) {
                    $tagAssignments = [];
                    foreach ($existingContactMap as $contactId) {
                        foreach ($tagIds as $tagId) {
                            $tagAssignments[] = [
                                'contact_id' => $contactId,
                                'tag_id' => $tagId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                    if (!empty($tagAssignments)) {
                        DB::table('contact_tag')->upsert(
                            $tagAssignments,
                            ['contact_id', 'tag_id'],
                            ['updated_at']
                        );
                    }
                }

                $importedCount = $createdCount + $updatedCount;

            }

            if($total == 0 || $importedCount == 0) {
                $message = 'No valid contacts to import';
            }else if($createdCount > 0 && $updatedCount == 0) {
                $message = "Successfully created ($createdCount) " . ($createdCount == 1 ? 'contact' : 'contacts' );
            }else if($createdCount == 0 && $updatedCount > 0) {
                $message = "Successfully updated ($updatedCount) " . ($updatedCount == 1 ? 'contact' : 'contacts' );
            }else if($createdCount > 0 && $updatedCount > 0) {
                $message = "Successfully created ($createdCount) and updated ($updatedCount) " . ($updatedCount == 1 ? 'contact' : 'contacts' );
            }

            if($importedCount == 0) {
                $percentageImported = 0;
            }else{
                $percentageImported = $importedCount/$total * 100 . '%';
            }

            $noImport = $importedCount == 0;
            $fullImport = $importedCount > 0 && $importedCount == $total;
            $partialImport = $importedCount > 0 && $importedCount < $total;

            return [
                'total' => $total,
                'errors' => $errors,
                'message' => $message,
                'no_import' => $noImport,
                'created' => $createdCount,
                'updated' => $updatedCount,
                'imported' => $importedCount,
                'full_import' => $fullImport,
                'partial_import' => $partialImport,
                'percentage_imported' => $percentageImported
            ];

        });
    }

    /**
     * Show contact.
     *
     * @param string $contactId
     * @return ContactResource
     */
    public function showContact(string $contactId): ContactResource
    {
        $contact = Contact::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($contactId);
        return $this->showResource($contact);
    }

    /**
     * Update contact.
     *
     * @param string $contactId
     * @param array $data
     * @return array
     */
    public function updateContact(string $contactId, array $data): array
    {
        $contact = Contact::findOrFail($contactId);
        $contact->update($data);

        return $this->showUpdatedResource($contact);
    }

    /**
     * Delete contact.
     *
     * @param string $contactId
     * @return array
     */
    public function deleteContact(string $contactId): array
    {
        $contact = Contact::findOrFail($contactId);

        $deleted = $contact->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Contact deleted'];
        }

        return ['deleted' => false, 'message' => 'Contact delete unsuccessful'];
    }

    /**
     * Delete contacts.
     *
     * @param string|null $organizationId
     * @param array $contactIds
     * @return array
     */
    public function deleteContacts(?string $organizationId, array $contactIds): array
    {
        $query = Contact::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $contactIds);

        $contacts = $query->get();

        if ($totalContacts = $contacts->count()) {
            $contacts->each->delete();
            return ['deleted' => true, 'message' => "$totalContacts contact(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No contacts deleted'];
    }

    /**
     * Prepare custom attributes for insertion (new contacts).
     *
     * @param string $contactId
     * @param array $contactData
     * @param array &$attributes
     * @return void
     */
    private function prepareCustomAttributesInsert(string $contactId, array $contactData, array &$attributes): void
    {
        $customAttributes = CustomAttribute::where('organization_id', $contactData['organization_id'])
            ->get()
            ->keyBy('name');

        $attributeData = [];
        foreach ($contactData['custom_attributes'] as $attr) {
            $attributeData[$attr['name']] = $attr['value'];
        }

        foreach ($attributeData as $name => $value) {
            if (isset($customAttributes[$name])) {
                $attributes[] = [
                    'id' => Str::uuid(),
                    'contact_id' => $contactId,
                    'custom_attribute_id' => $customAttributes[$name]->id,
                    'name' => $name,
                    'type' => $customAttributes[$name]->type,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    }

    /**
     * Prepare custom attributes for update (existing contacts).
     *
     * @param string $contactId
     * @param array $contactData
     * @param array &$attributes
     * @return void
     */
    private function prepareCustomAttributesUpdate(string $contactId, array $contactData, array &$attributes): void
    {
        $customAttributes = CustomAttribute::where('organization_id', $contactData['organization_id'])
            ->get()
            ->keyBy('name');

        foreach ($contactData['custom_attributes'] as $attr) {
            $name = $attr['name'];
            $value = $attr['value'];
            if (isset($customAttributes[$name])) {
                $attributes[] = [
                    'contact_id' => $contactId,
                    'custom_attribute_id' => $customAttributes[$name]->id,
                    'name' => $name,
                    'type' => $customAttributes[$name]->type,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    }

}
