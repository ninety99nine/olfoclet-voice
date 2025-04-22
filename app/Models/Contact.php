<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'organization_id', 'favorite_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Search scope for contacts.
     */
    public function scopeSearch(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->whereHas('identifiers', fn ($idQuery) =>
                  $idQuery->where('value', 'like', '%' . $searchTerm . '%')
              )
              ->orWhereHas('customAttributes', fn ($caQuery) =>
                  $caQuery->where('value', 'like', '%' . $searchTerm . '%')
              )
              ->orWhereHas('tags', fn ($tagQuery) =>
                  $tagQuery->where('name', 'like', '%' . $searchTerm . '%')
              )
              ->orWhereHas('organization', fn ($orgQuery) =>
                  $orgQuery->where('name', 'like', '%' . $searchTerm . '%')
              );
        });
    }

    /**
     * Organization relationship.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Favorite user relationship.
     */
    public function favoriteUser()
    {
        return $this->belongsTo(User::class, 'favorite_user_id');
    }

    /**
     * Calls relationship.
     */
    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    /**
     * Identifiers relationship.
     */
    public function identifiers()
    {
        return $this->hasMany(ContactIdentifier::class);
    }

    /**
     * Custom attributes relationship.
     */
    public function customAttributes()
    {
        return $this->hasMany(ContactCustomAttribute::class);
    }

    /**
     * Tags relationship.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'contact_tag')
                    ->withTimestamps();
    }

    /**
     * Get primary phone number.
     */
    protected function primaryPhone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('identifiers')
                ? collect($this->identifiers)->where('type', 'phone')->sortByDesc('is_primary')->first()?->value
                : null,
        );
    }

    /**
     * Get primary email.
     */
    protected function primaryEmail(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('identifiers')
                ? collect($this->identifiers)->where('type', 'email')->sortByDesc('is_primary')->first()?->value
                : null,
        );
    }

    /**
     * Get name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('customAttributes')
                ? collect($this->customAttributes)->where('name', 'name')->first()?->value
                : null
        );
    }

    /**
     * Set custom attribute.
     */
    public function setCustomAttribute(string $name, mixed $value, string $type): void
    {
        $allowedTypes = ['string', 'url', 'number', 'date'];

        if (!in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException("Invalid type: {$type}. Must be one of: " . implode(', ', $allowedTypes));
        }

        $customAttribute = CustomAttribute::firstOrCreate(
            [
                'organization_id' => $this->organization_id,
                'name' => $name,
                'type' => $type,
            ]
        );

        $this->customAttributes()->updateOrCreate(
            [
                'custom_attribute_id' => $customAttribute->id,
                'name' => $name,
            ],
            [
                'type' => $type,
                'value' => json_encode($value),
            ]
        );
    }
}
