<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContentItemResources extends ResourceCollection
{
    public $collects = ContentItemResource::class;
}
