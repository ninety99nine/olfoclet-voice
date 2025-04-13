<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\Organisation\CreateOrganizationRequest;
use App\Http\Requests\Organisation\UpdateOrganizationRequest;
use App\Services\OrganizationService;

class OrganizationController extends Controller
{
    protected $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->list();
    }

    public function create(CreateOrganizationRequest $request)
    {
        return $this->service->create($request->validated());
    }

    public function show(Organization $organization)
    {
        return $organization;
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        return $this->service->update($organization, $request->validated());
    }

    public function destroy(Organization $organization)
    {
        $this->service->delete($organization);
        return response()->json(null, 204);
    }
}


