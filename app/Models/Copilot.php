<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Copilot extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'organization_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function knowledgeBases()
    {
        return $this->belongsToMany(KnowledgeBase::class, 'copilot_knowledge_base', 'copilot_id', 'knowledge_base_id')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'copilot_user', 'copilot_id', 'user_id')
                    ->withTimestamps();
    }
}
