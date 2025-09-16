<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\CookiesServiceProvider::class,
    App\Providers\FolioServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
    Xetaio\Mentions\Providers\MentionServiceProvider::class,
];
