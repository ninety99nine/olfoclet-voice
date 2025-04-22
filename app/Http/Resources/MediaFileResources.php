<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaFileResources extends ResourceCollection
{
    public $collects = MediaFileResource::class;
}
