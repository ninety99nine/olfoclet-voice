<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'calls_count' => $this->whenCounted('calls'),
            'contacts_count' => $this->whenCounted('contacts'),
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            '_links' => [
                'self' => route('show-tag', ['tag' => $this->id]),
                'update' => route('update-tag', ['tag' => $this->id]),
                'delete' => route('delete-tag', ['tag' => $this->id]),
            ],
        ];
    }
}
