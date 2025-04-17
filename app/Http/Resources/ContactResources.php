<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContactResources extends ResourceCollection
{
    public $collects = ContactResource::class;
}
