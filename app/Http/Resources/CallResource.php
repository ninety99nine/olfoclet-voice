<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
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
            'from' => $this->from,
            'to' => $this->to,
            'direction' => $this->direction,
            'status' => $this->status,
            'session_id' => $this->session_id,
            'organization_id' => $this->organization_id,
            'agent_id' => $this->agent_id,
            'contact_id' => $this->contact_id,
            'queue_id' => $this->queue_id,
            'department_id' => $this->department_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'activities_count' => $this->whenCounted('activities'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'agent' => UserResource::make($this->whenLoaded('agent')),
            'contact' => ContactResource::make($this->whenLoaded('contact')),
            'queue' => QueueResource::make($this->whenLoaded('queue')),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            '_links' => [
                'self' => route('show-call', ['call' => $this->id]),
                'update' => route('update-call', ['call' => $this->id]),
                'delete' => route('delete-call', ['call' => $this->id]),
            ],
        ];
    }
}
