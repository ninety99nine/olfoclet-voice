<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function calls()
    {
        return $this->belongsToMany(Call::class, 'call_tag')
                    ->withTimestamps();
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_tag')
                    ->withTimestamps();
    }
}
