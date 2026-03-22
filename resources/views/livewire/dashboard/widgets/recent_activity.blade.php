<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
                <h3 class="h6 mb-1">{{ __('Recent activity') }}</h3>
                <p class="small text-body-secondary mb-0">{{ __('The latest changes across the work you can see.') }}</p>
            </div>
        </div>

        @if(empty($data['groups']))
            <p class="small text-body-secondary mb-0">{{ __('No activity yet.') }}</p>
        @else
            <div class="d-flex flex-column gap-4">
                @foreach($data['groups'] as $group)
                    <div>
                        <div class="text-uppercase small fw-semibold text-body-secondary">
                            {{ $group['label'] }}
                        </div>

                        <div class="d-flex flex-column gap-3 mt-2">
                            @foreach($group['items'] as $item)
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <img
                                            src="{{ $item['actor_avatar'] ?? asset('images/default-avatar.png') }}"
                                            alt=""
                                            class="rounded-circle object-fit-cover"
                                            style="width: 32px; height: 32px;"
                                        >

                                        <div class="min-w-0 flex-grow-1">
                                            <div class="small">
                                                <span class="fw-medium">{{ $item['actor_name'] }}</span>
                                                <span class="text-body-secondary">{{ strtolower($item['verb']) }}</span>

                                                @if($item['target_url'])
                                                    <a href="{{ $item['target_url'] }}" class="fw-medium text-decoration-underline">
                                                        {{ $item['target_label'] }}
                                                    </a>
                                                @else
                                                    <span class="fw-medium">{{ $item['target_label'] }}</span>
                                                @endif
                                            </div>

                                            <div class="small text-body-secondary mt-1">
                                                {{ $item['ago'] }}
                                            </div>

                                            @if(!empty($item['changes']))
                                                <div x-data="{ open: false }" class="mt-2">
                                                    <button type="button"
                                                            class="btn btn-link btn-sm p-0 text-decoration-underline"
                                                            @click="open = !open">
                                                        <span x-show="!open">{{ __('Show details') }}</span>
                                                        <span x-show="open">{{ __('Hide details') }}</span>
                                                    </button>

                                                    <div x-show="open" x-cloak class="mt-2 rounded-3 p-3 bg-body-tertiary d-flex flex-column gap-2 small">
                                                        @foreach($item['changes'] as $change)
                                                            <div class="d-flex align-items-start gap-2">
                                                                <div class="text-body-secondary" style="width: 7rem;">
                                                                    {{ $change['label'] }}
                                                                </div>
                                                                <div class="d-inline-flex align-items-center gap-2 flex-wrap">
                                                                    <span class="badge bg-white text-body border">{{ $change['from'] ?? '—' }}</span>
                                                                    <span>→</span>
                                                                    <span class="badge border"
                                                                          @if($change['to_color']) style="background-color: {{ $change['to_color'] }}15;" @endif>
                                                                        {{ $change['to'] ?? '—' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
