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
use Filament\Support\Colors\Color;
use Filament\Widgets;
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
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                ManageGeneralSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                ->label('Analytics')
                ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make()
                ->label('Settings')
                ->icon('heroicon-o-cog'),
            ])
            ->navigationItems([
                NavigationItem::make()
                ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                ->icon('heroicon-o-home')
                ->url(fn () => Dashboard::getUrl())
                ->sort(0)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
                NavigationItem::make()
                ->label(fn (): string => ManageGeneralSettings::getNavigationLabel())
                ->url(fn () => ManageGeneralSettings::getUrl())
                ->sort(0)
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.general-settings'))
                ->group('Settings'),
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
