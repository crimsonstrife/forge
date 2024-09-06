<?php

namespace App\Traits;

use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;
use App\Traits\HasPermissions as HasPermissions;

trait HasRoles
{
    //
    use SpatieHasRoles;
    use HasPermissions;
}
