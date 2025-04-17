<?php

namespace App\Services;

use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\DepartmentResources;

class DepartmentService extends BaseService
{
    /**
     * Show departments.
     *
     * @param string|null $organizationId
     * @return DepartmentResources|array
     */
    public function showDepartments(?string $organizationId = null): DepartmentResources|array
    {
        $query = Department::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create department.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createDepartment(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $department = Department::create($data);

        return $this->showCreatedResource($department);
    }

    /**
     * Show department.
     *
     * @param string $departmentId
     * @return DepartmentResource
     */
    public function showDepartment(string $departmentId): DepartmentResource
    {
        $department = Department::query()
            ->findOrFail($departmentId);

        return $this->showResource($department);
    }

    /**
     * Update department.
     *
     * @param string $departmentId
     * @param array $data
     * @return array
     */
    public function updateDepartment(string $departmentId, array $data): array
    {
        $department = Department::findOrFail($departmentId);
        $department->update($data);

        return $this->showUpdatedResource($department);
    }

    /**
     * Delete department.
     *
     * @param string $departmentId
     * @return array
     */
    public function deleteDepartment(string $departmentId): array
    {
        $department = Department::findOrFail($departmentId);

        $deleted = $department->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Department deleted'];
        }

        return ['deleted' => false, 'message' => 'Department delete unsuccessful'];
    }

    /**
     * Delete departments.
     *
     * @param string|null $organizationId
     * @param array $departmentIds
     * @return array
     */
    public function deleteDepartments(?string $organizationId, array $departmentIds): array
    {
        $query = Department::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $departmentIds);

        $departments = $query->get();

        if ($totalDepartments = $departments->count()) {
            $departments->each->delete();
            return ['deleted' => true, 'message' => "$totalDepartments department(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No departments deleted'];
    }
}
