<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomAttribute extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'organization_id', 'name', 'type'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contactCustomAttributes()
    {
        return $this->hasMany(ContactCustomAttribute::class);
    }
}
