<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">{{ __('Connected Accounts') }}</h5>
    </div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif

        <div class="d-flex flex-column gap-3">
            @foreach($this->providers as $provider)
                @php
                    $linked = collect($this->accounts)->firstWhere('provider', $provider);
                    $providerLabel = ucfirst($provider);
                @endphp

                <div class="d-flex align-items-center justify-content-between border rounded p-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge text-bg-secondary">{{ $providerLabel }}</span>
                        @if($linked)
                            <div>
                                <div class="fw-semibold">{{ $linked->nickname ?? $linked->provider_user_id }}</div>
                                <small class="text-body-secondary">
                                    {{ __('Linked on :date', ['date' => $linked->created_at->format('Y-m-d H:i')]) }}
                                </small>
                            </div>
                        @else
                            <div class="text-body-secondary">{{ __('Not linked') }}</div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        @if(!$linked)
                            <a href="{{ route('social.link', ['provider' => $provider]) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('Connect') }}
                            </a>
                        @else
                            <form method="POST" action="{{ route('social.unlink', ['socialAccount' => $linked]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    {{ __('Disconnect') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
