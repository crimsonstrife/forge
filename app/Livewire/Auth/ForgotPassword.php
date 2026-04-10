<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth.card')]
class ForgotPassword extends Component
{
    public $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $validated = $this->validate([
            'email' => ['bail', 'required', 'string', 'email'],
        ]);

        Password::sendResetLink(['email' => $validated['email']]);

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }
}
