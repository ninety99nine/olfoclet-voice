<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TagResources extends ResourceCollection
{
    public $collects = TagResource::class;
}
