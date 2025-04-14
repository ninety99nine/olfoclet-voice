<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\OrganizationService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\OrganizationResources;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;

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
     * @return OrganizationResources
     */
    public function showOrganizations(): OrganizationResources
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
     * @param string $organizationId
     * @return JsonResponse
     */
    public function showOrganization(string $organizationId): JsonResponse
    {
        return $this->prepareOutput($this->service->showOrganization($organizationId));
    }

    /**
     * Update organization.
     *
     * @param UpdateOrganizationRequest $request
     * @param string $organizationId
     * @return JsonResponse
     */
    public function updateOrganization(UpdateOrganizationRequest $request, string $organizationId): JsonResponse
    {
        return $this->prepareOutput($this->service->updateOrganization($organizationId, $request->validated()));
    }

    /**
     * Delete organization.
     *
     * @param string $organizationId
     * @return JsonResponse
     */
    public function deleteOrganization(string $organizationId): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteOrganization($organizationId));
    }
}
