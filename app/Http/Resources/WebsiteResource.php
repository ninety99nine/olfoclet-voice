<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteResource extends JsonResource
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
            'url' => $this->url,
            'ai_searchable' => $this->ai_searchable,
            'sync_status' => $this->sync_status,
            'last_synced_at' => $this->last_synced_at ? $this->last_synced_at->toDateString() : null,
            'organization_id' => $this->organization_id,
            'knowledge_base_id' => $this->knowledge_base_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'pages_count' => $this->whenCounted('pages'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'knowledge_base' => KnowledgeBaseResource::make($this->whenLoaded('knowledgeBase')),
            '_links' => [
                'self' => route('show-website', ['website' => $this->id]),
                'update' => route('update-website', ['website' => $this->id]),
                'delete' => route('delete-website', ['website' => $this->id]),
                'crawl_status' => route('check-crawl-status', ['website' => $this->id]),
            ],
        ];
    }
}
