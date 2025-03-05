<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center justify-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Word Search Game
            </h1>
            <p class="text-gray-600 mt-2">Find all the hidden words in the grid!</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <!-- Left Panel - Game Controls -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-lg text-gray-700">
                            Welcome, {{ auth()->user()->name }}!
                        </div>
                        <div class="text-2xl font-mono bg-gray-100 px-4 py-2 rounded-lg" x-data="timer()" x-init="start()">
                            <span x-text="formatTime(time)">00:00</span>
                        </div>
                    </div>
                    <!-- Reset Game Button -->
                    <button wire:click="resetGame" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                        Reset Game
                    </button>
                </div>

                <!-- Game Grid -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="grid gap-1 w-fit mx-auto" style="grid-template-columns: repeat({{ $gridSize }}, minmax(0, 1fr));"
                        x-data="wordSelection()"
                        @mouseup="checkSelection($wire)"
                        @touchend="checkSelection($wire)"
                        @word-found.window="handleWordFound($event.detail)">
                        @foreach ($grid as $i => $row)
                            @foreach ($row as $j => $letter)
                                <div class="w-10 h-10 flex items-center justify-center text-lg font-semibold rounded-md transition-colors cursor-pointer select-none"
                                    :class="{
                                        'bg-blue-200 text-blue-800 scale-105 shadow-md': isSelected({{ $i }}, {{ $j }}),
                                        'bg-green-200 text-green-800': isFoundWord({{ $i }}, {{ $j }}),
                                        'hover:bg-gray-100': !isSelected({{ $i }}, {{ $j }}) && !isFoundWord({{ $i }}, {{ $j }})
                                    }"
                                    @mousedown="startSelection({{ $i }}, {{ $j }})"
                                    @mouseover="updateSelection({{ $i }}, {{ $j }})"
                                    @touchstart.prevent="startSelection({{ $i }}, {{ $j }})"
                                    @touchmove.prevent="handleTouchMove($event, {{ $i }}, {{ $j }})">
                                    {{ $letter }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    <p class="text-gray-500 text-sm text-center mt-6">
                        Click and drag to select words. Words can be placed horizontally, vertically, or diagonally.
                    </p>
                </div>
            </div>

            <!-- Right Panel - Words to Find -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Words to Find</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($this->words as $word)
                        <div class="px-3 py-2 rounded {{ in_array($word, $foundWords) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $word }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function timer() {
            return {
                time: 0,
                interval: null,
                formatTime(seconds) {
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                    return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
                },
                start() {
                    this.interval = setInterval(() => {
                        this.time++;
                        @this.updateTimer(this.time);
                    }, 1000);
                },
                stop() {
                    clearInterval(this.interval);
                }
            }
        }

        function wordSelection() {
            return {
                selecting: false,
                selection: [],
                foundCoordinates: new Set(),

                init() {
                    // Initialize with any previously found words
                    @this.foundWordCoordinates.forEach(coords => {
                        coords.forEach(([i, j]) => {
                            this.foundCoordinates.add(`${i}-${j}`);
                        });
                    });
                },

                handleWordFound(detail) {
                    const { coordinates } = detail;
                    coordinates.forEach(([i, j]) => {
                        this.foundCoordinates.add(`${i}-${j}`);
                    });
                },

                startSelection(i, j) {
                    this.selecting = true;
                    this.selection = [[i, j]];
                },

                updateSelection(i, j) {
                    if (!this.selecting) return;
                    
                    // Only add if it's adjacent to the last selected cell
                    const last = this.selection[this.selection.length - 1];
                    const dx = Math.abs(last[0] - i);
                    const dy = Math.abs(last[1] - j);
                    
                    if ((dx <= 1 && dy <= 1) && !this.isSelected(i, j)) {
                        this.selection.push([i, j]);
                    }
                },

                handleTouchMove(event, i, j) {
                    const touch = event.touches[0];
                    const element = document.elementFromPoint(touch.clientX, touch.clientY);
                    if (element) {
                        this.updateSelection(i, j);
                    }
                },

                checkSelection($wire) {
                    if (this.selection.length > 0) {
                        $wire.checkSelection(this.selection).then(result => {
                            if (result) {
                                this.selection.forEach(([i, j]) => {
                                    this.foundCoordinates.add(`${i}-${j}`);
                                });
                            }
                        });
                    }
                    this.selecting = false;
                    this.selection = [];
                },

                isSelected(i, j) {
                    return this.selection.some(([x, y]) => x === i && y === j);
                },

                isFoundWord(i, j) {
                    return this.foundCoordinates.has(`${i}-${j}`);
                }
            }
        }
    </script>

    <style>
        /* Add these styles to your CSS */
        .scale-105 {
            transform: scale(1.05);
            transition: all 0.15s ease;
        }

        .shadow-md {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        /* Prevent text selection */
        .select-none {
            user-select: none;
            -webkit-user-select: none;
        }

        /* Add animation for found words */
        @keyframes highlight {
            0% { background-color: rgba(167, 243, 208, 0); }
            50% { background-color: rgba(167, 243, 208, 1); }
            100% { background-color: rgba(167, 243, 208, 0.5); }
        }

        .bg-green-200 {
            animation: highlight 0.5s ease-in-out;
        }
    </style>
</div> 