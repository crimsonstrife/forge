<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Security extends Cluster
{
    protected static ?string $navigationIcon = 'far-shield';

    protected static ?string $activeNavigationIcon = 'fas-shield';

    protected static ?string $navigationGroup = 'Application';
}
