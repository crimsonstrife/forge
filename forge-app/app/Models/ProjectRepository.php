<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRepository extends Model
{
    use HasFactory;

    protected $fillable = [
        'remote_id',
        'name',
        'description',
        'slug',
        'http_url',
        'ssh_url',
        'scm_type'
    ];
}
