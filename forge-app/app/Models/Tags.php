<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Class Tags
 *
 * This class represents the Tags model.
 * It extends the base Model class and uses the HasFactory trait.
 *
 * @package App\Models
 */
class Tags extends Model
{
    use HasFactory;
    use IsPermissable;
}
