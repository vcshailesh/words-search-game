<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->timestamps();
        });

        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_list_id')->constrained()->cascadeOnDelete();
            $table->string('word');
            $table->timestamps();
        });

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('word_list_id')->constrained()->cascadeOnDelete();
            $table->json('grid');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->integer('grid_size');
            $table->integer('time_taken')->nullable();
            $table->integer('words_found')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });

        Schema::create('found_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('word_id')->constrained()->cascadeOnDelete();
            $table->integer('time_taken');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_words');
        Schema::dropIfExists('games');
        Schema::dropIfExists('words');
        Schema::dropIfExists('word_lists');
    }
};
