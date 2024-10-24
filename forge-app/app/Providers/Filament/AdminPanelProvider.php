<?php

namespace App\Providers\Filament;

use App\Contracts\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Dashboard;
use App\Filament\Pages\ManageGeneralSettings;
use App\Http\Middleware\RoleMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Theme;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Enums\ThemeMode;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/**
 * Class AdminPanelProvider
 *
 * The AdminPanelProvider class is responsible for providing the admin panel configuration for the Filament application.
 */
class AdminPanelProvider extends PanelProvider
{
    /**
     * Boot the panel provider.
     */
    public function boot(): void
    {
        // Register the icon overrides for the Filament admin panel.
        FilamentIcon::register([
            'panels::global-search.field' => 'far-magnifying-glass',
            'panels::global-search.clear' => 'far-circle-xmark',
            'panels::pages.dashboard.actions.filter' => 'far-filter',
            'panels::pages.dashboard.actions.sort' => 'far-sort',
            'panels::pages.dashboard.actions.view' => 'far-eye',
            'panels::pages.dashboard.filters.clear' => 'far-times',
            'panels::pages.dashboard.filters.apply' => 'far-check',
            'panels::user-menu.profile-item' => 'far-circle-user',
            'panels::user-menu.account-item' => 'far-user-gear',
            'panels::user-menu.logout-button' => 'far-left-to-bracket',
            'panels::sidebar.group.collapse-button' => 'far-angle-up',
            'forms::components.checkbox-list.search-field' => 'far-magnifying-glass',
            'tables::search-field' => 'far-magnifying-glass',
            'tables::actions.view' => 'far-eye',
            'tables::actions.filter' => 'far-filter',
            'tables::actions.sort' => 'far-sort',
            'tables::header-cell.sort-asc-button' => 'far-sort-up',
            'tables::header-cell.sort-desc-button' => 'far-sort-down',
            'tables::columns.icon-column.false' => 'far-circle-xmark',
            'tables::columns.icon-column.true' => 'far-circle-check',
            'notifications::notification.danger' => 'far-triangle-exclamation',
            'notifications::notification.info' => 'far-circle-info',
            'notifications::notification.success' => 'far-circle-check',
            'notifications::notification.warning' => 'far-circle-exclamation',
            'notifications::notification.close-button' => 'far-circle-xmark',
        ]);
    }

    /**
     * Define the panel for the Filament admin panel.
     *
     * @param Panel $panel The panel instance.
     * @return Panel The modified panel instance.
     */
    public function panel(Panel $panel): Panel
    {

        // Return the panel configuration.
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureFrontendRequestsAreStateful::class,
            ])
            ->authMiddleware([
                Authenticate::class => 'web',
            ])
            ->authGuard('sanctum');
    }
}
