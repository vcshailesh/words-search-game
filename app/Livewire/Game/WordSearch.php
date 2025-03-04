<?php

namespace App\Livewire\Game;

use App\Models\Game;
use App\Models\Word;
use App\Models\WordList;
use App\Services\WordSearchService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class WordSearch extends Component
{
    public ?Game $game = null;
    public array $grid = [];
    public array $wordLocations = [];
    public array $foundWords = [];
    public string $difficulty = 'easy';
    public int $gridSize = 8;
    public int $timer = 0;
    public bool $gameStarted = false;
    public array $selection = [];

    public function mount()
    {
        $this->difficulty = 'easy';
        $this->gridSize = 8; // Fixed size for easy mode
        $this->resetGame();
    }

    public function startGame()
    {
        $this->gameStarted = true;
        $this->dispatch('start-timer');
    }

    public function resetGame()
    {
        $wordList = WordList::where('difficulty', $this->difficulty)->first();
        $words = $wordList->words->pluck('word')->toArray();

        $service = new WordSearchService();
        $result = $service->generateGrid($words, $this->gridSize);

        $this->grid = $result['grid'];
        $this->wordLocations = $result['wordLocations'];
        $this->foundWords = [];
        $this->timer = 0;
        $this->gameStarted = false;

        $this->game = Game::create([
            'user_id' => auth()->id(),
            'word_list_id' => $wordList->id,
            'grid' => $this->grid,
            'difficulty' => $this->difficulty,
            'grid_size' => $this->gridSize,
        ]);
    }

    #[Computed]
    public function words()
    {
        return WordList::where('difficulty', $this->difficulty)
            ->first()
            ->words()
            ->pluck('word')
            ->map(fn($word) => strtoupper($word))
            ->toArray();
    }

    public function checkSelection(array $selection)
    {
        $selectedWord = strtoupper($this->getWordFromSelection($selection));
        $reverseWord = strrev($selectedWord);

        // Check both forward and reverse directions
        if ((!in_array($selectedWord, $this->foundWords) && in_array($selectedWord, $this->words())) ||
            (!in_array($reverseWord, $this->foundWords) && in_array($reverseWord, $this->words()))
        ) {

            $wordToAdd = in_array($selectedWord, $this->words()) ? $selectedWord : $reverseWord;
            $this->foundWords[] = $wordToAdd;
            $this->dispatch('word-found', word: $wordToAdd);

            if (count($this->foundWords) === count($this->words())) {
                $this->completeGame();
            }
        }
    }

    private function getWordFromSelection(array $selection): string
    {
        $word = '';
        foreach ($selection as $coordinates) {
            $word .= $this->grid[$coordinates[0]][$coordinates[1]];
        }
        return $word;
    }

    private function completeGame()
    {
        $this->game->update([
            'completed' => true,
            'time_taken' => $this->timer,
            'words_found' => count($this->foundWords)
        ]);

        $this->dispatch('game-completed');
    }

    public function updateTimer(int $time)
    {
        $this->timer = $time;
    }

    public function render()
    {
        return view('livewire.game.word-search')
            ->title('Word Search Game');
    }
}
