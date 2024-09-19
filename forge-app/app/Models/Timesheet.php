<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Timesheet Model
 *
 * This class represents the Timesheet model in the application.
 * It extends the base Model class and uses the HasFactory trait.
 */
class Timesheet extends Model
{
    use HasFactory;
    use IsPermissable;
}
