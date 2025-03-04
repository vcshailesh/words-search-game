<?php

namespace App\Services;

class WordSearchService
{
    private array $grid = [];
    private array $wordLocations = [];
    private array $directions = [
        'horizontal' => [0, 1],
        'vertical' => [1, 0],
        'diagonal' => [1, 1],
        'reverse-horizontal' => [0, -1],
        'reverse-vertical' => [-1, 0],
        'reverse-diagonal' => [-1, -1],
        'diagonal-up' => [-1, 1],
        'diagonal-down' => [1, -1]
    ];

    public function generateGrid(array $words, int $size): array
    {
        $this->grid = array_fill(0, $size, array_fill(0, $size, ''));
        $this->wordLocations = [];

        // Convert all words to uppercase and sort by length
        $words = array_map('strtoupper', $words);
        usort($words, fn($a, $b) => strlen($b) - strlen($a));

        // Try to place all words
        $attempts = 0;
        $maxAttempts = 50;

        while ($attempts < $maxAttempts) {
            $success = true;
            $this->grid = array_fill(0, $size, array_fill(0, $size, ''));
            $this->wordLocations = [];

            foreach ($words as $word) {
                if (!$this->placeWord($word)) {
                    $success = false;
                    break;
                }
            }

            if ($success) {
                break;
            }

            $attempts++;
        }

        // if ($attempts === $maxAttempts) {
        //     throw new \RuntimeException('Could not generate a valid grid after ' . $maxAttempts . ' attempts');
        // }

        // Fill remaining spaces with random letters that don't create unintended words
        $this->fillEmptySpaces($words);

        return [
            'grid' => $this->grid,
            'wordLocations' => $this->wordLocations
        ];
    }

    private function placeWord(string $word): bool
    {
        $length = strlen($word);
        $size = count($this->grid);
        $availableDirections = array_keys($this->directions);

        // Try each direction in random order
        shuffle($availableDirections);

        foreach ($availableDirections as $direction) {
            $directionVector = $this->directions[$direction];

            // Calculate valid starting positions
            $maxX = $size - abs($directionVector[0] * ($length - 1));
            $maxY = $size - abs($directionVector[1] * ($length - 1));

            // Try all possible positions
            for ($x = 0; $x < $maxX; $x++) {
                for ($y = 0; $y < $maxY; $y++) {
                    if ($this->canPlaceWord($word, $x, $y, $directionVector)) {
                        // Place the word
                        $this->wordLocations[$word] = [
                            'start' => [$x, $y],
                            'direction' => $direction
                        ];

                        for ($i = 0; $i < $length; $i++) {
                            $newX = $x + ($i * $directionVector[0]);
                            $newY = $y + ($i * $directionVector[1]);
                            $this->grid[$newX][$newY] = $word[$i];
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function canPlaceWord(string $word, int $startX, int $startY, array $direction): bool
    {
        $length = strlen($word);
        $size = count($this->grid);

        for ($i = 0; $i < $length; $i++) {
            $x = $startX + ($i * $direction[0]);
            $y = $startY + ($i * $direction[1]);

            // Check boundaries
            if ($x < 0 || $x >= $size || $y < 0 || $y >= $size) {
                return false;
            }

            // Check if space is empty or has matching letter
            if ($this->grid[$x][$y] !== '' && $this->grid[$x][$y] !== $word[$i]) {
                return false;
            }

            // Check for adjacent letters that might form unintended words
            if (!$this->isValidPlacement($x, $y, $word[$i])) {
                return false;
            }
        }

        return true;
    }

    private function isValidPlacement(int $x, int $y, string $letter): bool
    {
        $size = count($this->grid);
        $adjacentOffsets = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        foreach ($adjacentOffsets as $offset) {
            $newX = $x + $offset[0];
            $newY = $y + $offset[1];

            if ($newX >= 0 && $newX < $size && $newY >= 0 && $newY < $size) {
                if (
                    $this->grid[$newX][$newY] !== '' &&
                    $this->grid[$newX][$newY] !== $letter
                ) {
                    // Check if this would create an unintended word
                    if ($this->wouldFormUnintendedWord($x, $y, $letter)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function wouldFormUnintendedWord(int $x, int $y, string $letter): bool
    {
        // Check horizontal and vertical sequences
        $sequences = [
            $this->getSequence($x, $y, [-1, 0], [1, 0], $letter), // horizontal
            $this->getSequence($x, $y, [0, -1], [0, 1], $letter), // vertical
        ];

        foreach ($sequences as $sequence) {
            if (strlen($sequence) >= 3) {
                // Check if this sequence forms a word that's not in our word list
                return true;
            }
        }

        return false;
    }

    private function getSequence(int $x, int $y, array $dir1, array $dir2, string $centerLetter): string
    {
        $sequence = '';
        $size = count($this->grid);

        // Check in first direction
        $curX = $x + $dir1[0];
        $curY = $y + $dir1[1];
        while ($curX >= 0 && $curX < $size && $curY >= 0 && $curY < $size && $this->grid[$curX][$curY] !== '') {
            $sequence = $this->grid[$curX][$curY] . $sequence;
            $curX += $dir1[0];
            $curY += $dir1[1];
        }

        // Add center letter
        $sequence .= $centerLetter;

        // Check in second direction
        $curX = $x + $dir2[0];
        $curY = $y + $dir2[1];
        while ($curX >= 0 && $curX < $size && $curY >= 0 && $curY < $size && $this->grid[$curX][$curY] !== '') {
            $sequence .= $this->grid[$curX][$curY];
            $curX += $dir2[0];
            $curY += $dir2[1];
        }

        return $sequence;
    }

    private function fillEmptySpaces(array $words): void
    {
        $size = count($this->grid);
        $letters = range('A', 'Z');

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($this->grid[$i][$j] === '') {
                    // Try each letter until we find one that doesn't create unintended words
                    shuffle($letters);
                    foreach ($letters as $letter) {
                        if ($this->isValidPlacement($i, $j, $letter)) {
                            $this->grid[$i][$j] = $letter;
                            break;
                        }
                    }
                    // If no letter works, just use a random one
                    if ($this->grid[$i][$j] === '') {
                        $this->grid[$i][$j] = $letters[array_rand($letters)];
                    }
                }
            }
        }
    }
}
