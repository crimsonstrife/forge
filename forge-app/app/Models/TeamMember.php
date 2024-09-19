<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissable;

/**
 * Class TeamMember
 *
 * This class represents a team member in the application.
 * It extends the Model class and uses the HasFactory trait.
 */
class TeamMember extends Model
{
    use HasFactory;
    use IsPermissable;
}
