<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaFileResource extends JsonResource
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
            'path' => $this->path,
            'size' => $this->size,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'organization_id' => $this->organization_id,
            'call_flow_nodes' => CallFlowNodeResource::collection($this->whenLoaded('callFlowNodes')),
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            '_links' => [
                'self' => route('show-media-file', ['mediaFile' => $this->id]),
                'update' => route('update-media-file', ['mediaFile' => $this->id]),
                'delete' => route('delete-media-file', ['mediaFile' => $this->id]),
            ],
        ];
    }
}
