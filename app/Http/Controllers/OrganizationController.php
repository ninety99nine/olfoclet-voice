<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\OrganizationService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\OrganizationResources;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;

class OrganizationController extends BaseController
{
    /**
     * @var OrganizationService
     */
    protected $service;

    /**
     * OrganizationController constructor.
     *
     * @param OrganizationService $service
     */
    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }

    /**
     * Show organizations.
     *
     * @return OrganizationResources|JsonResponse
     */
    public function showOrganizations(): OrganizationResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showOrganizations());
    }

    /**
     * Create organization.
     *
     * @param CreateOrganizationRequest $request
     * @return JsonResponse
     */
    public function createOrganization(CreateOrganizationRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createOrganization($request->validated()));
    }

    /**
     * Delete multiple organizations.
     *
     * @return JsonResponse
     */
    public function deleteOrganizations(): JsonResponse
    {
        $organizationIds = request()->input('organization_ids', []);
        return $this->prepareOutput($this->service->deleteOrganizations($organizationIds));
    }

    /**
     * Show organization by alias.
     *
     * @param string $alias
     * @return JsonResponse
     */
    public function showOrganizationByAlias(string $alias): JsonResponse
    {
        return $this->prepareOutput($this->service->showOrganizationByAlias($alias));
    }

    /**
     * Show single organization.
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function showOrganization(Organization $organization): JsonResponse
    {
        return $this->prepareOutput($this->service->showOrganization($organization->id));
    }

    /**
     * Update organization.
     *
     * @param UpdateOrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function updateOrganization(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        return $this->prepareOutput($this->service->updateOrganization($organization->id, $request->validated()));
    }

    /**
     * Delete organization.
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function deleteOrganization(Organization $organization): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteOrganization($organization->id));
    }
}
