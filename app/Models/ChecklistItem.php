<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ChecklistItem extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'checklist_id',
        'description',
        'is_completed',
        'completed_at',
        'sort_order',

    ];
    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
