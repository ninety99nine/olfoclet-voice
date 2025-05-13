<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentSource extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'type', 'name', 'last_synced_at', 'knowledge_base_id'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('type', 'like', '%' . $searchTerm . '%')
              ->orWhere('name', 'like', '%' . $searchTerm . '%');
        });
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class, 'source_id');
    }
}
