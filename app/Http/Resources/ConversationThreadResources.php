<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationThreadResources extends ResourceCollection
{
    public $collects = ConversationThreadResource::class;
}
