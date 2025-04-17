<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'queues_count' => $this->whenCounted('queues'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-department', ['department' => $this->id]),
                'update' => route('update-department', ['department' => $this->id]),
                'delete' => route('delete-department', ['department' => $this->id]),
            ],
        ];
    }
}
