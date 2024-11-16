<?php

namespace App\Models;

use App\Models\Projects\Project;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'is_shared', 'owner_id'];

    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dashboard')->withTimestamps();
    }
}
