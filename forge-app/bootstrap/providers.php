<?php

/**
 * File: forge/forge-app/bootstrap/providers.php
 *
 * This file contains an array of service providers that are registered in the application.
 *
 * Service providers are responsible for bootstrapping various components of the application
 * and registering them with the Laravel service container. They are used to bind classes
 * into the container, register event listeners, and perform other necessary bootstrapping tasks.
 *
 * The service providers listed in this array will be loaded and registered by the Laravel framework
 * during the application's bootstrap process.
 *
 * @return array
 */
return [
    /*
    * Laravel Framework Service Providers...
    */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,
    Laravel\Horizon\HorizonServiceProvider::class,
    Laravel\Fortify\FortifyServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,

    /*
    * Application Service Providers...
    */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServerProvider::class,
    App\Providers\ConfigProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,

    /*
    * Package Service Providers...
    */
    Xetaio\Mentions\Providers\MentionServiceProvider::class,

    /*
    * Spatie Permissions Provider...
    */
    Spatie\Permission\PermissionServiceProvider::class,
];
