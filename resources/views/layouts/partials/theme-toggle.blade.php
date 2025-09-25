<div x-data="{ current: (localStorage.getItem('theme') || 'auto') }" class="d-flex align-items-center">
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <wa-icon family="solid" name="circle-half-stroke"></wa-icon>
            <span class="ms-1 d-none d-md-inline">Theme</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><button type="button" class="dropdown-item d-flex align-items-center gap-2" @click="window.__setTheme('light'); current='light'"><wa-icon family="solid" name="sun"></wa-icon> Light</button></li>
            <li><button type="button" class="dropdown-item d-flex align-items-center gap-2" @click="window.__setTheme('dark'); current='dark'"><wa-icon family="solid" name="moon"></wa-icon> Dark</button></li>
            <li><button type="button" class="dropdown-item d-flex align-items-center gap-2" @click="window.__setTheme('auto'); current='auto'"><wa-icon family="solid" name="computer"></wa-icon> Auto</button></li>
        </ul>
    </div>
</div>
