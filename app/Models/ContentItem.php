<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentItem extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'ai_ingested' => 'boolean',
            'copilot_enabled' => 'boolean',
            'ai_agent_enabled' => 'boolean',
            'help_center_enabled' => 'boolean',
        ];
    }

    protected $fillable = [
        'title', 'content', 'locale', 'ai_ingested', 'copilot_enabled',
        'ai_agent_enabled', 'help_center_enabled', 'visibility', 'state',
        'type', 'parent_id', 'source_id', 'help_center_collection_id', 'knowledge_base_id'
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('content', 'like', '%' . $searchTerm . '%');
        });
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function parent()
    {
        return $this->belongsTo(ContentItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ContentItem::class, 'parent_id');
    }

    public function source()
    {
        return $this->belongsTo(ContentSource::class);
    }

    public function helpCenterCollection()
    {
        return $this->belongsTo(HelpCenterCollection::class);
    }
}
