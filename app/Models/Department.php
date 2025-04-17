<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'description', 'active', 'organization_id'
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('organization', fn ($orgQuery) =>
                  $orgQuery->search($searchTerm)
              );
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function fallbackQueues()
    {
        return $this->hasMany(Queue::class, 'fallback_department_id');
    }
}
