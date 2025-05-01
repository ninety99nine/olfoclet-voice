<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SnippetResources extends ResourceCollection
{
    public $collects = SnippetResource::class;
}
