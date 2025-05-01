<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Website extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'url',
        'ai_searchable',
        'sync_status',
        'last_synced_at',
        'organization_id',
        'knowledge_base_id',
    ];

    protected function casts(): array
    {
        return [
            'organization_id' => 'string',
            'knowledge_base_id' => 'string',
            'ai_searchable' => 'boolean',
            'sync_status' => 'string',
            'last_synced_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('url', 'like', '%' . $searchTerm . '%');
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

    public function websitePages()
    {
        return $this->hasMany(WebsitePage::class);
    }
}
