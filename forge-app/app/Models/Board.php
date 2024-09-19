<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Class Board
 * Model for the Board table
 * @package App\Models
 */
class Board extends Model
{
    use HasFactory;
    use IsPermissable;
}
