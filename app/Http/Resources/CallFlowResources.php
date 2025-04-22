<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CallFlowResources extends ResourceCollection
{
    public $collects = CallFlowResource::class;
}
