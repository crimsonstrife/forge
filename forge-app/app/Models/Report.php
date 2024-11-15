<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['title', 'description', 'content', 'dashboard_id'];

    /**
     * Define the Dashboard relationship.
     *
     * @return mixed The dashboard data.
     */
    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }

    /**
     * Define the owner relationship.
     *
     * @return mixed The owner data.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the user that owns the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
