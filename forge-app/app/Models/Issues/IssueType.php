<?php

namespace App\Models\Issues;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Facades\Log;
use App\Models\Issues\Issue;
use App\Models\Icon;
use App\Traits\IsPermissable;

/**
 * Class IssueType
 *
 * This class represents the IssueType model in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models\Issues\IssueType
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $icon
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Issues\Issue[] $issues
 * @property int|null $issues_count
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType newQuery()
 * @method static \Illuminate\Database\Query\Builder|IssueType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType query()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|IssueType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IssueType withoutTrashed()
 * @mixin \Eloquent
 */
class IssueType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissable;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_default',
        'description'
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Clear the cache when Issue Types are saved or deleted
        static::saved(function () {
            cache()->forget('issue_types.all');
        });

        // Clear the cache when Issue Types are saved or deleted
        static::deleted(function () {
            cache()->forget('issue_types.all');
        });

        static::saving(function ($model) {
            $query = static::where('name', $model->name)
                ->where('icon', $model->icon)
                ->where('color', $model->color);

            if ($model->exists) {
                $query->where('id', '!=', $model->id);
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'name' => 'Issue type with this name, icon, and color already exists, please make changes to avoid duplication.'
                ]);
            }
        });
    }

    /**
     * Relationship with the icon model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Icon::class, 'icon', 'id');
    }

    /**
     * Get the default issue type.
     *
     * @return \Illuminate\Database\Eloquent\Builder|IssueType
     */
    public static function getDefault(): \Illuminate\Database\Eloquent\Builder|IssueType
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Get the issues associated with this issue type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'issue_type_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Scope a query to only include default issue priorities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include issue priorities that are not default.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDefault($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_default', false);
    }

    /**
     * Get the issue type by name.
     *
     * @param string $name
     * @return IssueType
     */
    public static function getByName(string $name): IssueType
    {
        return self::where('name', $name)->first();
    }

    /**
     * Generic method to find IssueType by column and value or fail
     *
     * @param string $column
     * @param mixed $value
     * @return IssueType
     * @throws RuntimeException
     */
    public static function findByColumnOrFail(string $column, mixed $value): IssueType
    {
        $issueType = self::whereColumn($column, $value);

        if ($issueType === null) {
            throw new \RuntimeException('IssueType not found');
        }

        return $issueType;
    }

    /**
     * Retrieves the first IssueType by given column and value
     *
     * @param string $column
     * @param mixed $value
     * @return IssueType|null
     */
    private static function whereColumn(string $column, mixed $value): ?IssueType
    {
        return self::where($column, $value)->first();
    }

    /**
     * Where
     * Alias for findByColumnOrFail
     *
     * @param string $column
     * @param mixed $value
     * @return IssueType
     * @throws RuntimeException
     */
    public static function where(string $column, mixed $value): IssueType
    {
        return self::findByColumnOrFail($column, $value);
    }
}
