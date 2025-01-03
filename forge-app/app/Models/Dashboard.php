<?php

namespace App\Models;

use App\Models\Projects\Project;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\IsPermissible;

class Dashboard extends Model
{
    use SoftDeletes;
    use IsPermissible;

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
        return $this->belongsToMany(Report::class, 'dashboard_report', 'dashboard_id', 'report_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dashboard', 'dashboard_id', 'user_id')->withTimestamps();
    }
}
