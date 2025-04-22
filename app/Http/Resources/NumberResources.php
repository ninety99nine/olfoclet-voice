<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NumberResources extends ResourceCollection
{
    public $collects = NumberResource::class;
}
