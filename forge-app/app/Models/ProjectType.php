<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProjectType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'description',
        'is_default'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'type_id', 'id');
    }

    /**
     * Is the project type the default type?
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Get the color of the project type
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Get the description of the project type
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
