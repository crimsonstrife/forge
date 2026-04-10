<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Authorize :app', ['app' => $client->name]) }} &mdash; {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">

<nav class="navbar bg-body border-bottom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <x-application-logo />
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h5 class="card-title mb-1">{{ __('Authorization Request') }}</h5>
                    <p class="text-body-secondary small mb-4">
                        {{ __(':app is requesting access to your account.', ['app' => $client->name]) }}
                    </p>

                    @if (count($scopes) > 0)
                        <div class="mb-4">
                            <p class="fw-semibold small mb-2">{{ __('This application will be able to:') }}</p>
                            <ul class="list-unstyled mb-0">
                                @foreach ($scopes as $scope)
                                    <li class="d-flex align-items-start gap-2 mb-2 small">
                                        <i class="fas fa-check-circle text-success mt-1 flex-shrink-0"></i>
                                        <span>{{ $scope->description }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <!-- Authorize -->
                        <form method="post" action="{{ route('passport.authorizations.approve') }}" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="state" value="{{ $request->state }}">
                            <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                            <input type="hidden" name="auth_token" value="{{ $authToken }}">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Authorize') }}
                            </button>
                        </form>

                        <!-- Deny -->
                        <form method="post" action="{{ route('passport.authorizations.deny') }}" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="state" value="{{ $request->state }}">
                            <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                            <input type="hidden" name="auth_token" value="{{ $authToken }}">
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                {{ __('Deny') }}
                            </button>
                        </form>
                    </div>

                    <p class="text-body-secondary text-center mt-3 mb-0" style="font-size: 0.78rem;">
                        {{ __('You are logged in as :name (:email)', ['name' => $user->name, 'email' => $user->email]) }}
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
