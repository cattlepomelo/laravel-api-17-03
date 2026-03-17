<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupInternship extends Model
{
    protected $fillable = [
        'group_id',
        'internship_id',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }
}
