<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone_number',
        'metadata', 'notes', 'organization_id'
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('email', 'like', '%' . $searchTerm . '%')
              ->orWhere('phone_number', 'like', '%' . $searchTerm . '%')
              ->orWhere('notes', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('organization', fn ($orgQuery) =>
                  $orgQuery->search($searchTerm)
              );
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }
}
