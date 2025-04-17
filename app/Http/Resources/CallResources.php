<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CallResources extends ResourceCollection
{
    public $collects = CallResource::class;
}
