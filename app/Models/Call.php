<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Call extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'direction', 'status', 'from', 'to',
        'started_at', 'answered_at', 'ended_at', 'hold_time', 'duration',
        'disposition', 'transfer_count', 'recording_url', 'session_id',
        'metadata', 'notes', 'ai_summary', 'ai_suggested_actions',
        'organization_id', 'queue_id', 'department_id', 'agent_id', 'contact_id'
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'answered_at' => 'datetime',
            'ended_at' => 'datetime',
            'hold_time' => 'integer',
            'duration' => 'integer',
            'transfer_count' => 'integer',
            'metadata' => 'array',
            'ai_suggested_actions' => 'array',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('from', 'like', '%' . $searchTerm . '%')
              ->orWhere('to', 'like', '%' . $searchTerm . '%')
              ->orWhere('notes', 'like', '%' . $searchTerm . '%')
              ->orWhere('ai_summary', 'like', '%' . $searchTerm . '%')
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

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function callActivities()
    {
        return $this->hasMany(CallActivity::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'call_tag')
                    ->withTimestamps();
    }
}
