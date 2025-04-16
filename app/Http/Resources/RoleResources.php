<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleResources extends ResourceCollection
{
    public $collects = RoleResource::class;
}
