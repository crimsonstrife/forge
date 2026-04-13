<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\CookiesServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FolioServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HealthServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\ModelObserverServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\SoloModeServiceProvider;
use App\Providers\TelescopeServiceProvider;
use App\Providers\VoltServiceProvider;
use Laravel\Passport\PassportServiceProvider;
use SocialiteProviders\Manager\ServiceProvider;
use Xetaio\Mentions\Providers\MentionServiceProvider;

return [
    PassportServiceProvider::class,
    AppServiceProvider::class,
    AuthServiceProvider::class,
    CookiesServiceProvider::class,
    EventServiceProvider::class,
    AdminPanelProvider::class,
    FolioServiceProvider::class,
    FortifyServiceProvider::class,
    HealthServiceProvider::class,
    HorizonServiceProvider::class,
    JetstreamServiceProvider::class,
    ModelObserverServiceProvider::class,
    RepositoryServiceProvider::class,
    SoloModeServiceProvider::class,
    TelescopeServiceProvider::class,
    VoltServiceProvider::class,
    ServiceProvider::class,
    MentionServiceProvider::class,
];
