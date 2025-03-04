<?php

namespace Database\Seeders;

use App\Models\Word;
use App\Models\WordList;
use Illuminate\Database\Seeder;

class WordListSeeder extends Seeder
{
    public function run(): void
    {
        // Easy Words (3-4 letters)
        $easyList = WordList::create([
            'name' => 'Word Search',
            'difficulty' => 'easy',
        ]);

        // Easy words organized in related groups for better grid placement
        $easyWords = [
            'CAT',
            'DOG',
            'SUN',
            'RUN',
            'HAT',
            'MAP',
            'BUG',
            'CUP',
            'PEN',
            'BOX'
        ];

        foreach ($easyWords as $word) {
            Word::create([
                'word_list_id' => $easyList->id,
                'word' => $word,
            ]);
        }
    }
}
