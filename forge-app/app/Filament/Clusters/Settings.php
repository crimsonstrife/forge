<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'far-gears';

    protected static ?string $activeNavigationIcon = 'fas-gears';

    protected static ?string $navigationGroup = 'Application';
}
