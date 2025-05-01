<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationThread extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id', 'user_id', 'copilot_id', 'title'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where('title', 'like', '%' . $searchTerm . '%');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function copilot(): BelongsTo
    {
        return $this->belongsTo(Copilot::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'thread_id');
    }
}
