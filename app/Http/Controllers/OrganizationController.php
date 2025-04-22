<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use App\Services\OrganizationService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\OrganizationResources;
use App\Http\Requests\Organization\ShowOrganizationRequest;
use App\Http\Requests\Organization\ShowOrganizationsRequest;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use App\Http\Requests\Organization\DeleteOrganizationsRequest;
use App\Http\Requests\Organization\ShowOrganizationByAliasRequest;

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
     * @param ShowOrganizationsRequest $request
     * @return OrganizationResources|JsonResponse
     */
    public function showOrganizations(ShowOrganizationsRequest $request): OrganizationResources|JsonResponse
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
        return $this->prepareOutput($this->service->createOrganization($request->validated()), 201);
    }

    /**
     * Delete multiple organizations.
     *
     * @param DeleteOrganizationsRequest $request
     * @return JsonResponse
     */
    public function deleteOrganizations(DeleteOrganizationsRequest $request): JsonResponse
    {
        $organizationIds = request()->input('organization_ids', []);
        return $this->prepareOutput($this->service->deleteOrganizations($organizationIds));
    }

    /**
     * Show organization by alias.
     *
     * @param ShowOrganizationByAliasRequest $request
     * @param string $alias
     * @return JsonResponse
     */
    public function showOrganizationByAlias(ShowOrganizationByAliasRequest $request, string $alias): JsonResponse
    {
        return $this->prepareOutput($this->service->showOrganizationByAlias($alias));
    }

    /**
     * Show single organization.
     *
     * @param ShowOrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function showOrganization(ShowOrganizationRequest $request, Organization $organization): JsonResponse
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
     * @param DeleteOrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function deleteOrganization(DeleteOrganizationRequest $request, Organization $organization): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteOrganization($organization->id));
    }
}
