<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProduct extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /** @return BelongsTo<Organization,ServiceProduct> */
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }

    /** @return BelongsTo<Project,ServiceProduct> */
    public function defaultProject(): BelongsTo { return $this->belongsTo(Project::class, 'default_project_id'); }

    /** @return BelongsToMany<Project> */
    public function projects(): BelongsToMany { return $this->belongsToMany(Project::class, 'project_service_product'); }

    /** @return HasMany<Ticket> */
    public function tickets(): HasMany { return $this->hasMany(Ticket::class, 'service_product_id'); }
}
