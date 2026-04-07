<section>
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="{{ __('Theme') }}">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.__setTheme('light')">
                        {{ __('Light') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.__setTheme('dark')">
                        {{ __('Dark') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.__setTheme('auto')">
                        {{ __('System') }}
                    </button>
                </div>

                <p class="text-body-secondary small mb-0 mt-3">
                    {{ __('Theme preferences are stored locally in this browser, matching the app-wide theme toggle.') }}
                </p>
            </div>
        </div>
    </x-settings.layout>
</section>
