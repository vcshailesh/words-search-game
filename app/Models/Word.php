<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Word extends Model
{
    protected $fillable = ['word_list_id', 'word'];

    public function wordList(): BelongsTo
    {
        return $this->belongsTo(WordList::class);
    }
}
