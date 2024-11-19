<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

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
    use IsPermissible;

    protected $fillable = ['name', 'slug', 'color', 'icon', 'display_only_on_item_cards', 'created_by', 'updated_by', 'deleted_by'];

    /**
     * Get the projects associated with the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_has_tags', 'tag_id', 'project_id')->withTimestamps();
    }

    /**
     * Get the issues associated with the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues()
    {
        return $this->belongsToMany(Issue::class, 'issue_has_tags', 'tag_id', 'issue_id')->withTimestamps();
    }
}
