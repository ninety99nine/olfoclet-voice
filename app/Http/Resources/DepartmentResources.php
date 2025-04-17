<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DepartmentResources extends ResourceCollection
{
    public $collects = DepartmentResource::class;
}
