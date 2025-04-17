<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\DepartmentResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;

class DepartmentController extends BaseController
{
    /**
     * @var DepartmentService
     */
    protected $service;

    /**
     * DepartmentController constructor.
     *
     * @param DepartmentService $service
     */
    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Show departments.
     *
     * @return DepartmentResources|JsonResponse
     */
    public function showDepartments(): DepartmentResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showDepartments(request('organization_id')));
    }

    /**
     * Create department.
     *
     * @param CreateDepartmentRequest $request
     * @return JsonResponse
     */
    public function createDepartment(CreateDepartmentRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createDepartment(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single department.
     *
     * @param Department $department
     * @return JsonResponse
     */
    public function showDepartment(Department $department): JsonResponse
    {
        return $this->prepareOutput($this->service->showDepartment($department->id));
    }

    /**
     * Update department.
     *
     * @param UpdateDepartmentRequest $request
     * @param Department $department
     * @return JsonResponse
     */
    public function updateDepartment(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        return $this->prepareOutput($this->service->updateDepartment($department->id, $request->validated()));
    }

    /**
     * Delete department.
     *
     * @param Department $department
     * @return JsonResponse
     */
    public function deleteDepartment(Department $department): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteDepartment($department->id));
    }

    /**
     * Delete multiple departments.
     *
     * @return JsonResponse
     */
    public function deleteDepartments(): JsonResponse
    {
        $departmentIds = request()->input('department_ids', []);
        return $this->prepareOutput($this->service->deleteDepartments(request('organization_id'), $departmentIds));
    }
}
