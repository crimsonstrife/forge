<div class="row g-4 align-items-start">
    <aside class="col-12 col-md-3 col-xl-2">
        <nav class="list-group list-group-flush border rounded-3 overflow-hidden" aria-label="{{ __('Settings') }}">
            <a href="{{ route('profile.show') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('profile.show', 'settings.profile') ? 'active' : '' }}">
                {{ __('Profile') }}
            </a>
            <a href="{{ route('settings.password') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('settings.password') ? 'active' : '' }}">
                {{ __('Password') }}
            </a>
            <a href="{{ route('settings.appearance') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('settings.appearance') ? 'active' : '' }}">
                {{ __('Appearance') }}
            </a>
        </nav>
    </aside>

    <section class="col-12 col-md-9 col-xl-8">
        <div class="mb-4">
            <h2 class="h4 mb-1">{{ $heading ?? '' }}</h2>
            <p class="text-body-secondary mb-0">{{ $subheading ?? '' }}</p>
        </div>

        <div class="vstack gap-4" style="max-width: 42rem;">
            {{ $slot }}
        </div>
    </section>
</div>
