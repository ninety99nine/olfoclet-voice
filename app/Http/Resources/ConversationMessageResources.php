<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationMessageResources extends ResourceCollection
{
    public $collects = ConversationMessageResource::class;
}
