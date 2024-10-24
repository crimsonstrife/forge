<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Facades\Auth;

class Analytics extends BasePage
{
    protected static ?string $title = 'Application Analytics';

    protected static ?int $navigationSort = -1;

    protected static string $routePath = '/analytics';

    protected static ?string $navigationIcon = 'far-chart-mixed';

    protected static ?string $activeNavigationIcon = 'fas-chart-mixed';

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
