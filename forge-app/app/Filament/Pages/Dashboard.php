<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static bool $shouldRegisterNavigation = false;

    public function getColumns(): int | array
    {
        return 6;
    }

    public function getWidgets(): array
    {
        return [

        ];
    }
}
