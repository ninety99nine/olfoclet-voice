<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CallFlowNode extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'call_flow_id',
        'type',
        'next_step',
        'metadata',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'position' => 'array',
    ];

    /**
     * Get the call flow that owns the node.
     *
     * @return BelongsTo
     */
    public function callFlow(): BelongsTo
    {
        return $this->belongsTo(CallFlow::class);
    }

    /**
     * Get the media files associated with the node.
     *
     * @return BelongsToMany
     */
    public function mediaFiles(): BelongsToMany
    {
        return $this->belongsToMany(MediaFile::class, 'call_flow_node_media_file')
                    ->withTimestamps();
    }
}
