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
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServerProvider::class,
    App\Providers\ConfigProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
];
