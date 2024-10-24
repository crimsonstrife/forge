<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Issues extends Cluster
{
    protected static ?string $navigationIcon = 'far-ticket';

    protected static ?string $activeNavigationIcon = 'fas-ticket';

    protected static ?string $navigationGroup = 'Features';
}
