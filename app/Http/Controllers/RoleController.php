<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\RoleResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;

class RoleController extends BaseController
{
    /**
     * @var RoleService
     */
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /**
     * Show roles.
     *
     * @return RoleResources|JsonResponse
     */
    public function showRoles(): RoleResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showRoles(request('organization_id')));
    }

    /**
     * Create role.
     *
     * @param CreateRoleRequest $request
     * @return JsonResponse
     */
    public function createRole(CreateRoleRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createRole(request('organization_id'), $request->validated()));
    }

    /**
     * Delete multiple roles.
     *
     * @return JsonResponse
     */
    public function deleteRoles(): JsonResponse
    {
        $roleIds = request()->input('role_ids', []);
        return $this->prepareOutput($this->service->deleteRoles(request('organization_id'), $roleIds));
    }

    /**
     * Show single role.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function showRole(Role $role): JsonResponse
    {
        return $this->prepareOutput($this->service->showRole($role->id));
    }

    /**
     * Update role.
     *
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return JsonResponse
     */
    public function updateRole(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        return $this->prepareOutput($this->service->updateRole($role->id, $request->validated()));
    }

    /**
     * Delete role.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function deleteRole(Role $role): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteRole($role->id));
    }
}
