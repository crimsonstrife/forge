<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Projects extends Cluster
{
    protected static ?string $navigationIcon = 'far-diagram-project';

    protected static ?string $activeNavigationIcon = 'fas-diagram-project';

    protected static ?string $navigationGroup = 'Features';
}
