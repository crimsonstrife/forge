<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Class DesignElements
 * Model for the DesignElements table
 * @package App\Models
 */
class DesignElements extends Model
{
    use HasFactory;
    use IsPermissable;
}
