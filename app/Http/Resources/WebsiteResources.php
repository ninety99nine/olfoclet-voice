<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WebsiteResources extends ResourceCollection
{
    public $collects = WebsiteResource::class;
}
