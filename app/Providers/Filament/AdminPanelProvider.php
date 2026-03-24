<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Filament\Pages\ConnectorsAndSyncSettings;
use App\Filament\Pages\Reports\ProjectAnalytics;
use App\Filament\Widgets\AssigneeWorkloadTable;
use App\Filament\Widgets\CumulativeFlowChart;
use App\Filament\Widgets\ProjectHealthStats;
use App\Filament\Widgets\ThroughputTrend;
use App\Http\Middleware\SetPermissionsTeamContext;
use App\Listeners\SwitchTeam;
use Filament\Events\TenantSet;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                ConnectorsAndSyncSettings::class,
                ProjectAnalytics::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                AssigneeWorkloadTable::class,
                CumulativeFlowChart::class,
                ProjectHealthStats::class,
                ThroughputTrend::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                SetPermissionsTeamContext::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentLogViewer::make()
                    ->navigationGroup('System')
                    ->authorize(function () {
                        $user = auth()->user();
                        return $user
                            && (
                                $user->can('view.system-logs') ||
                                $user->can('is-super-admin') ||
                                $user->can('is-admin')
                            );
                    }),
        ]);
    }
}
