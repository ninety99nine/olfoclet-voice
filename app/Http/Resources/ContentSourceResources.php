<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContentSourceResources extends ResourceCollection
{
    public $collects = ContentSourceResource::class;
}
