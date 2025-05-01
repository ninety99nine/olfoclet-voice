<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Snippet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'content',
        'ai_searchable',
        'organization_id',
        'knowledge_base_id',
    ];

    protected function casts(): array
    {
        return [
            'organization_id' => 'string',
            'knowledge_base_id' => 'string',
            'ai_searchable' => 'boolean',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('content', 'like', '%' . $searchTerm . '%');
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class);
    }
}
