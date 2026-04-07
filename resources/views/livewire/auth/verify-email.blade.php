<div class="vstack gap-4 text-center">
    <p class="text-body-secondary mb-0">
        {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-0" role="alert">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="d-grid gap-3">
        <button type="button" class="btn btn-primary" wire:click="sendVerification">
            {{ __('Resend verification email') }}
        </button>

        <button type="button" class="btn btn-link text-body-secondary text-decoration-none" wire:click="logout">
            {{ __('Log out') }}
        </button>
    </div>
</div>
