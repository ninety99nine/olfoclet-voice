<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HelpCenterCollection extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [];
    }

    protected $fillable = [
        'name', 'knowledge_base_id'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%');
        });
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class);
    }
}
