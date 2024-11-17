<?php

namespace App\Models\Templates;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ReportTemplate
 *
 * This class represents a report template in the application.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\Templates
 */
class ReportTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'content', 'settings', 'filters'];

    // Cast JSON fields
    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'filters' => 'array',
    ];

    /**
     * Get the reports associated with the report template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
