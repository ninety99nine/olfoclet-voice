<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class KnowledgeBaseResources extends ResourceCollection
{
    public $collects = KnowledgeBaseResource::class;
}
