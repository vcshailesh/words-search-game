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
    public array $foundWordCoordinates = [];

    public function mount()
    {
        // Check if user has an active game
        $this->game = Game::where('user_id', auth()->id())
            ->where('completed', false)
            ->latest()
            ->first();

        if ($this->game) {
            // Load existing game
            $this->grid = $this->game->grid;
            $this->difficulty = $this->game->difficulty;
            $this->gridSize = $this->game->grid_size;
            $this->timer = $this->game->timer;
            $this->loadSavedProgress();
        } else {
            // Start new game
            $this->startNewGame();
        }
    }

    private function loadSavedProgress()
    {
        // Load found words from the database
        $this->foundWords = $this->game->foundWords()
            ->with('word')
            ->get()
            ->pluck('word.word')
            ->map(fn($word) => strtoupper($word))
            ->toArray();

        // Load found word coordinates
        $this->foundWordCoordinates = $this->game->foundWords()
            ->pluck('coordinates')
            ->map(fn($coords) => json_decode($coords, true))
            ->toArray();
    }

    public function startNewGame()
    {
        $wordList = WordList::where('difficulty', 'easy')->first();
        $words = $wordList->words->pluck('word')->toArray();

        $service = new WordSearchService();
        $result = $service->generateGrid($words, 8); // Fixed size for easy mode

        $this->grid = $result['grid'];
        $this->wordLocations = $result['wordLocations'];
        $this->foundWords = [];
        $this->foundWordCoordinates = [];
        $this->timer = 0;
        $this->gameStarted = false;

        // Create new game record
        $this->game = Game::create([
            'user_id' => auth()->id(),
            'word_list_id' => $wordList->id,
            'grid' => $this->grid,
            'difficulty' => 'easy',
            'grid_size' => 8,
        ]);
    }

    public function resetGame()
    {
        // Mark current game as completed if exists
        if ($this->game) {
            $this->game->update(['completed' => true]);
        }

        // Start a new game
        $this->startNewGame();
    }

    #[Computed]
    public function words()
    {
        return WordList::where('difficulty', 'easy')
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

        if ((!in_array($selectedWord, $this->foundWords) && in_array($selectedWord, $this->words())) ||
            (!in_array($reverseWord, $this->foundWords) && in_array($reverseWord, $this->words()))
        ) {
            $wordToAdd = in_array($selectedWord, $this->words()) ? $selectedWord : $reverseWord;
            $this->foundWords[] = $wordToAdd;
            $this->foundWordCoordinates[] = $selection;

            // Save progress to database
            $word = Word::where('word', $wordToAdd)->first();
            $this->game->foundWords()->create([
                'word_id' => $word->id,
                'time_taken' => $this->timer,
                'coordinates' => json_encode($selection)
            ]);

            $this->dispatch('word-found', [
                'word' => $wordToAdd,
                'coordinates' => $selection
            ]);

            if (count($this->foundWords) === count($this->words())) {
                $this->completeGame();
            }

            return true;
        }

        return false;
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

        // Save the timer value to the database
        if ($this->game) {
            $this->game->update(['timer' => $this->timer]);
        }
    }

    public function render()
    {
        return view('livewire.game.word-search')
            ->title('Word Search Game');
    }
}
