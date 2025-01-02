<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Milestone Model
 *
 * Represents a milestone in the application.
 */
class Milestone extends Model
{
    use HasFactory;
    use IsPermissible;
}
