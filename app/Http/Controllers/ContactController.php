<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ContactResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Contact\ShowContactRequest;
use App\Http\Requests\Contact\ShowContactsRequest;
use App\Http\Requests\Contact\CreateContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Http\Requests\Contact\DeleteContactRequest;
use App\Http\Requests\Contact\ImportContactsRequest;
use App\Http\Requests\Contact\DeleteContactsRequest;

class ContactController extends BaseController
{
    /**
     * @var ContactService
     */
    protected $service;

    /**
     * ContactController constructor.
     *
     * @param ContactService $service
     */
    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    /**
     * Show contacts.
     *
     * @param ShowContactsRequest $request
     * @return ContactResources|JsonResponse
     */
    public function showContacts(ShowContactsRequest $request): ContactResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showContacts(request('organization_id')));
    }

    /**
     * Create contact.
     *
     * @param CreateContactRequest $request
     * @return JsonResponse
     */
    public function createContact(CreateContactRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createContact(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Import contacts from CSV.
     *
     * @param ImportContactsRequest $request
     * @return JsonResponse
     */
    public function importContacts(ImportContactsRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->importContacts(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single contact.
     *
     * @param ShowContactRequest $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function showContact(ShowContactRequest $request, Contact $contact): JsonResponse
    {
        return $this->prepareOutput($this->service->showContact($contact->id));
    }

    /**
     * Update contact.
     *
     * @param UpdateContactRequest $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function updateContact(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        return $this->prepareOutput($this->service->updateContact($contact->id, $request->validated()));
    }

    /**
     * Delete contact.
     *
     * @param DeleteContactRequest $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function deleteContact(DeleteContactRequest $request, Contact $contact): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteContact($contact->id));
    }

    /**
     * Delete multiple contacts.
     *
     * @param DeleteContactsRequest $request
     * @return JsonResponse
     */
    public function deleteContacts(DeleteContactsRequest $request): JsonResponse
    {
        $contactIds = request()->input('contact_ids', []);
        return $this->prepareOutput($this->service->deleteContacts(request('organization_id'), $contactIds));
    }
}
