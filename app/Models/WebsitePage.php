<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsitePage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'page_url',
        'content',
        'ai_searchable',
        'website_id',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'website_id' => 'string',
            'organization_id' => 'string',
            'ai_searchable' => 'boolean',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('page_url', 'like', '%' . $searchTerm . '%')
              ->orWhere('content', 'like', '%' . $searchTerm . '%');
        });
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
