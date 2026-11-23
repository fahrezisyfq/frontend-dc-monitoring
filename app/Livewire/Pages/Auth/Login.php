<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Login extends Component
{
    public function render(): View
    {
        return view('livewire.pages.auth.login')
            ->layout('layouts.guest', ['title' => 'Masuk']);
    }
}
