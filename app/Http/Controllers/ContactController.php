<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ContactResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Contact\CreateContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;

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
     * @return ContactResources|JsonResponse
     */
    public function showContacts(): ContactResources|JsonResponse
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
     * Show single contact.
     *
     * @param Contact $contact
     * @return JsonResponse
     */
    public function showContact(Contact $contact): JsonResponse
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
     * @param Contact $contact
     * @return JsonResponse
     */
    public function deleteContact(Contact $contact): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteContact($contact->id));
    }

    /**
     * Delete multiple contacts.
     *
     * @return JsonResponse
     */
    public function deleteContacts(): JsonResponse
    {
        $contactIds = request()->input('contact_ids', []);
        return $this->prepareOutput($this->service->deleteContacts(request('organization_id'), $contactIds));
    }
}
