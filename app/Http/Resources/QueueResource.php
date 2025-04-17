<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
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
            'department_id' => $this->department_id,
            'fallback_queue_id' => $this->fallback_queue_id,
            'fallback_department_id' => $this->fallback_department_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'calls_count' => $this->whenCounted('calls'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'fallback_queue' => QueueResource::make($this->whenLoaded('fallbackQueue')),
            'fallback_department' => DepartmentResource::make($this->whenLoaded('fallbackDepartment')),
            '_links' => [
                'self' => route('show-queue', ['queue' => $this->id]),
                'update' => route('update-queue', ['queue' => $this->id]),
                'delete' => route('delete-queue', ['queue' => $this->id]),
            ],
        ];
    }
}
