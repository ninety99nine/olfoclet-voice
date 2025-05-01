<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CopilotResource extends JsonResource
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
            'description' => $this->description,
            'organization_id' => $this->organization_id,
            'knowledge_bases' => KnowledgeBaseResource::collection($this->whenLoaded('knowledgeBases')),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-copilot', ['copilot' => $this->id]),
                'update' => route('update-copilot', ['copilot' => $this->id]),
                'delete' => route('delete-copilot', ['copilot' => $this->id]),
                'query' => route('query-copilot', ['copilot' => $this->id]),
            ],
        ];
    }
}
