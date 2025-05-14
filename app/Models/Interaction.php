<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interaction extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [];
    }

    protected $fillable = [
        'call_id', 'omni_channel_message_id', 'organization_id',
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->whereHas('organization', fn ($orgQuery) =>
                  $orgQuery->search($searchTerm)
              );
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function omniChannelMessage()
    {
        return $this->belongsTo(OmniChannelMessage::class);
    }
}
