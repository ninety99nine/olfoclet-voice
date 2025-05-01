<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleResources extends ResourceCollection
{
    public $collects = ArticleResource::class;
}
