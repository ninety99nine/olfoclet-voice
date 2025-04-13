<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrganizationResource;

class OrganizationService
{
    public function list()
    {
        $pagination = Organization::paginate();
        return OrganizationResource::collection($pagination);
    }

    public function create(array $data): Organization
    {
        return DB::transaction(fn () => Organization::create($data));
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);
        return $organization;
    }

    public function delete(Organization $organization): void
    {
        $organization->delete();
    }
}


