<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomAttributeResources extends ResourceCollection
{
    public $collects = CustomAttributeResource::class;
}
