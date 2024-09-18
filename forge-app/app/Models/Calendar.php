<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Class Calendar
 * Model for the Calendar table
 * @package App\Models
 */
class Calendar extends Model
{
    use HasFactory;
    use IsPermissable;
}
