<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OmniChannelMessage extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected $fillable = [
        'channel', 'direction', 'status', 'from', 'to',
        'content', 'message_type', 'external_message_id',
        'sent_at', 'delivered_at', 'read_at', 'error_message',
        'metadata', 'organization_id', 'contact_id', 'agent_id', 'queue_id',
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('from', 'like', '%' . $searchTerm . '%')
              ->orWhere('to', 'like', '%' . $searchTerm . '%')
              ->orWhere('content', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('organization', fn ($orgQuery) =>
                  $orgQuery->search($searchTerm)
              )
              ->orWhereHas('contact', fn ($contactQuery) =>
                  $contactQuery->where('phone_number', 'like', '%' . $searchTerm . '%')
                              ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                              ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
              );
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}
