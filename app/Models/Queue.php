<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Queue extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'last_sla_review' => 'datetime',
            'sla_threshold' => 'integer',
            'max_wait_time' => 'integer',
            'escalation_threshold' => 'integer',
            'avg_wait_time' => 'integer',
            'service_level' => 'decimal:2',
            'abandonment_rate' => 'decimal:2',
            'call_volume_warning_threshold' => 'integer',
            'call_volume_critical_threshold' => 'integer',
            'record_calls' => 'boolean',
            'callback_enabled' => 'boolean',
            'metadata' => 'array'
        ];
    }

    protected $fillable = [
        'name', 'description', 'active',
        'organization_id', 'department_id',
        'sla_threshold', 'max_wait_time', 'escalation_threshold',
        'avg_wait_time', 'service_level', 'abandonment_rate', 'last_sla_review',
        'call_volume_warning_threshold', 'call_volume_critical_threshold',
        'record_calls', 'strategy', 'priority_level', 'metadata',
        'hold_music_url', 'greeting_message', 'wait_message', 'callback_enabled',
        'fallback_queue_id', 'fallback_department_id'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('organization', fn ($orgQuery) =>
                  $orgQuery->search($searchTerm)
              )
              ->orWhereHas('department', fn ($deptQuery) =>
                  $deptQuery->search($searchTerm)
              );
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function fallbackQueue()
    {
        return $this->belongsTo(Queue::class, 'fallback_queue_id');
    }

    public function fallbackDepartment()
    {
        return $this->belongsTo(Department::class, 'fallback_department_id');
    }
}
