<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public bool $notify_on_assignment = true;

    public bool $notify_on_comment = true;

    public bool $notify_on_status_change = true;

    public bool $notify_on_link_change = true;

    public bool $notify_on_mention = true;

    public bool $daily_digest_enabled = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;

        $preferences = Auth::user()->issueNotificationPreference()->firstOrCreate([]);
        $this->notify_on_assignment = (bool) $preferences->notify_on_assignment;
        $this->notify_on_comment = (bool) $preferences->notify_on_comment;
        $this->notify_on_status_change = (bool) $preferences->notify_on_status_change;
        $this->notify_on_link_change = (bool) $preferences->notify_on_link_change;
        $this->notify_on_mention = (bool) $preferences->notify_on_mention;
        $this->daily_digest_enabled = (bool) $preferences->daily_digest_enabled;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->issueNotificationPreference()->updateOrCreate([], [
            'notify_on_assignment' => $this->notify_on_assignment,
            'notify_on_comment' => $this->notify_on_comment,
            'notify_on_status_change' => $this->notify_on_status_change,
            'notify_on_link_change' => $this->notify_on_link_change,
            'notify_on_mention' => $this->notify_on_mention,
            'daily_digest_enabled' => $this->daily_digest_enabled,
        ]);

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}
