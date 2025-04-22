<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactIdentifier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'contact_id', 'type', 'value', 'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
