<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBase extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'organization_id' => 'string',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%');
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contentSources()
    {
        return $this->hasMany(ContentSource::class);
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class);
    }

    public function helpCenterCollections()
    {
        return $this->hasMany(HelpCenterCollection::class);
    }
}
