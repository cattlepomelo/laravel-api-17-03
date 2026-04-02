<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Evaluation extends Model
{
    protected $fillable = ['application_id', 'date', 'grade', 'comment'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get all comments for the evaluation.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
