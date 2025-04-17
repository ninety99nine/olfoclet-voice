<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallActivityResource extends JsonResource
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
            'call_id' => $this->call_id,
            'activity_type' => $this->activity_type,
            'description' => $this->description,
            'performed_by' => $this->performed_by,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'call' => CallResource::make($this->whenLoaded('call')),
            'performed_by_user' => UserResource::make($this->whenLoaded('performedBy')),
            '_links' => [
                'self' => route('show-call-activity', ['callActivity' => $this->id]),
                'update' => route('update-call-activity', ['callActivity' => $this->id]),
                'delete' => route('delete-call-activity', ['callActivity' => $this->id]),
            ],
        ];
    }
}
