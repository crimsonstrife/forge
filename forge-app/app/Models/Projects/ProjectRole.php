<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Represents a project role in the application.
 *
 * This class extends the Laravel Model class and uses the HasFactory trait.
 * It is used to interact with the project_roles table in the database.
 */
class ProjectRole extends Model
{
    use HasFactory;
    use IsPermissible;
}
