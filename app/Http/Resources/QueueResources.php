<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QueueResources extends ResourceCollection
{
    public $collects = QueueResource::class;
}
