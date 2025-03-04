<?php

use App\Livewire\Auth\Register;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class);
Route::get('/register', Register::class)->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/game', App\Livewire\Game\WordSearch::class)->name('game');
});
