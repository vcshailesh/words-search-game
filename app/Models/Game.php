<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'user_id',
        'word_list_id',
        'grid',
        'difficulty',
        'grid_size',
        'time_taken',
        'words_found',
        'completed'
    ];

    protected $casts = [
        'grid' => 'array',
        'completed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wordList(): BelongsTo
    {
        return $this->belongsTo(WordList::class);
    }

    public function foundWords(): HasMany
    {
        return $this->hasMany(FoundWord::class);
    }
}
