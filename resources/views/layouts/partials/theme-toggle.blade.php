<div class="d-flex align-items-center gap-1" aria-label="{{ __('Theme') }}">
    <button type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="window.__setTheme('light')"
            title="{{ __('Light mode') }}"
            aria-label="{{ __('Light mode') }}">
        <wa-icon family="solid" name="sun"></wa-icon>
    </button>
    <button type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="window.__setTheme('dark')"
            title="{{ __('Dark mode') }}"
            aria-label="{{ __('Dark mode') }}">
        <wa-icon family="solid" name="moon"></wa-icon>
    </button>
    <button type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="window.__setTheme('auto')"
            title="{{ __('System theme') }}"
            aria-label="{{ __('System theme') }}">
        <wa-icon family="solid" name="circle-half-stroke"></wa-icon>
    </button>
</div>
