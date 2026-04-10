<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.auth.card')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $email = request()->input('email');

        $this->email = is_string($email) ? $email : '';
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $validated = $this->validate([
            'token' => ['bail', 'required'],
            'email' => ['bail', 'required', 'string', 'email'],
            'password' => ['bail', 'required', 'string', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['bail', 'required', 'string'],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'token' => $validated['token'],
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status !== Password::PasswordReset) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}
