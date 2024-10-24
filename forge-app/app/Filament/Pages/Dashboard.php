<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BasePage
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationIcon = 'far-house';

    protected static ?string $activeNavigationIcon = 'fas-house';

    public function getColumns(): int | array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [

        ];
    }
}
