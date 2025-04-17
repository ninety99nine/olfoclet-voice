<?php

namespace App\Services;

use App\Models\Contact;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResources;

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
        $data['organization_id'] = $organizationId;
        $contact = Contact::create($data);

        return $this->showCreatedResource($contact);
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
}
