<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'call_id', 'activity_type', 'description',
        'performed_by', 'performed_at', 'metadata'
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('activity_type', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('call', fn ($callQuery) =>
                  $callQuery->search($searchTerm)
              )
              ->orWhereHas('performedBy', fn ($userQuery) =>
                  $userQuery->search($searchTerm)
              );
        });
    }

    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
