<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactCustomAttribute extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'contact_id','custom_attribute_id','name','type','value'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function customAttribute()
    {
        return $this->belongsTo(CustomAttribute::class);
    }
}
