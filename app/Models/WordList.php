<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WordList extends Model
{
    protected $fillable = ['name', 'difficulty'];

    public function words(): HasMany
    {
        return $this->hasMany(Word::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
