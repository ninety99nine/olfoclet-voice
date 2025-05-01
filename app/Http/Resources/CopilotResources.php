<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CopilotResources extends ResourceCollection
{
    public $collects = CopilotResource::class;
}
