<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HelpCenterCollectionResource extends JsonResource
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
            'knowledge_base_id' => $this->knowledge_base_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'content_items_count' => $this->whenCounted('contentItems'),
            'knowledge_base' => KnowledgeBaseResource::make($this->whenLoaded('knowledgeBase')),
            '_links' => [
                'self' => route('show-help-center-collection', ['helpCenterCollection' => $this->id]),
                'update' => route('update-help-center-collection', ['helpCenterCollection' => $this->id]),
                'delete' => route('delete-help-center-collection', ['helpCenterCollection' => $this->id]),
            ],
        ];
    }
}
