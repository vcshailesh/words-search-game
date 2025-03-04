<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $user = User::create($validated);

        auth()->login($user);

        return $this->redirect('/game');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->title('Register');
    }
}
