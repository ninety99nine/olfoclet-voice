<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContactCustomAttributeResources extends ResourceCollection
{
    public $collects = ContactCustomAttributeResource::class;
}
