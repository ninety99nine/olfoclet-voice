<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationMessage extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'context' => 'array'
    ];

    protected $fillable = [
        'id', 'thread_id', 'role', 'content', 'context'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where('content', 'like', '%' . $searchTerm . '%');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ConversationThread::class);
    }
}
