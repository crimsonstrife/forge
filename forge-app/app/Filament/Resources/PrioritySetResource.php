<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Issues;
use App\Filament\Resources\PrioritySetResource\Pages;
use App\Filament\Resources\PrioritySetResource\RelationManagers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PrioritySet;
use App\Models\PivotModels\PrioritySetPriorities;
use App\Models\Issues\IssuePriority;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;


/**
 * Class PrioritySetResource
 *
 * This class represents a resource in the Filament admin panel for managing priority sets.
 * It extends the base Resource class provided by Filament.
 *
 * @package App\Filament\Resources
 */
class PrioritySetResource extends Resource
{
    protected static ?string $model = PrioritySet::class;

    protected static ?string $pivotModel = PrioritySetPriorities::class;

    protected static ?string $relatedResource = IssuePriority::class;

    protected static ?string $navigationIcon = 'far-list-radio';

    protected static ?string $activeNavigationIcon = 'fas-list-radio';

    protected static ?string $navigationGroup = 'Issues';

    protected static ?string $cluster = Issues::class;

    /**
     * Define the form schema for the PrioritySetResource.
     *
     * @param Form $form The form instance.
     * @return Form The configured form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Priority Set Name')
                    ->required(),

                // Repeater for issue priorities assigned to the priority set, defined by the pivot model PrioritySetPriorities,
                // Uses the priority_set_id and issue_priority_id columns to relate to the PrioritySet and IssuePriority models respectively.
                Forms\Components\Repeater::make('prioritySetPriorities')
                    ->relationship()
                    ->orderColumn('order')
                    ->schema([
                        Forms\Components\Select::make('issue_priority_id')
                            ->label('Issue Priority')
                            ->options(
                                fn() => IssuePriority::pluck('name', 'id')
                            ) // Lazy load to avoid pre-loading interference
                            ->default(
                                // Get the first default priority if it exists
                                IssuePriority::where('is_default', true)->first()?->id
                            )
                            ->required()
                            ->searchable()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->unique(),

                        Forms\Components\TextInput::make('order')
                            ->label('Order')
                            ->numeric()
                            //->hidden() // Hides the order field from the form
                            ->required()
                            ->default(0),  // Sets default order based on index

                        // Checkbox to indicate if the priority is the default for the set, only one priority can be the default
                        Forms\Components\Checkbox::make('is_default')
                            ->label('Default Priority')
                            ->default(false)
                            ->distinct()
                            ->fixIndistinctState()
                            ->inline(false),
                    ])
                    ->label('Issue Priorities')
                    ->columns(2)
                    ->minItems(1)
                    ->collapsible(true)
                    ->reorderableWithButtons(true)
                    ->reorderableWithDragAndDrop(true)
                    ->addActionLabel('Add Priority'),
            ]);
    }

    /**
     * Configure the table for the PrioritySetResource.
     *
     * @param Table $table The table instance to configure.
     * @return Table The configured table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Priority Set Name'),
                Tables\Columns\TextColumn::make('priorities_count')
                    ->label('Number of Priorities')
                    ->counts('priorities'),
            ])
            ->defaultSort('name');
    }


    /**
     * Get the form model.
     *
     * @return Model|string|null The form model, which can be an instance of Model, a string, or null.
     */
    public function getFormModel(): Model|string|null
    {
        return IssuePriority::class;
    }

    /**
     * Get the relations for the PrioritySet resource.
     *
     * @return array An array of relations.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages associated with the PrioritySet resource.
     *
     * @return array An array of pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrioritySets::route('/'),
            'create' => Pages\CreatePrioritySet::route('/create'),
            'edit' => Pages\EditPrioritySet::route('/{record}/edit'),
        ];
    }

    /**
     * Determine if the resource can be accessed.
     *
     * @return bool True if the resource can be accessed, false otherwise.
     */
    public static function canAccess(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'list-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            $canDo = $user->hasPermissionTo($permission, 'web');

            if ($canDo) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Determine whether any priority sets can be viewed.
     *
     * @return bool
     */
    public static function canViewAny(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'list-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if the given record can be viewed.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be viewed, false otherwise.
     */
    public static function canView(Model $record): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'read-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
            // TODO: Check if the authenticated user has the 'read' permission for the given record.
        }

        return false;
    }

    /**
     * Determine if a new Priority Set can be created.
     *
     * @return bool True if creation is allowed, false otherwise.
     */
    public static function canCreate(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'create-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if the given record can be edited.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be edited, false otherwise.
     */
    public static function canEdit(Model $record): bool
    {
        // alias for canUpdate
        return static::canUpdate($record);
    }

    /**
     * Determine if the given record can be updated.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be updated, false otherwise.
     */
    public static function canUpdate(Model $record): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'update-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if the given record can be deleted.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be deleted, false otherwise.
     */
    public static function canDelete(Model $record): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'delete-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if the given record can be restored.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be restored, false otherwise.
     */
    public static function canRestore(Model $record): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'restore-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine whether the user can force delete the given record.
     *
     * @param Model $record The record instance.
     * @return bool True if the user can force delete the record, false otherwise.
     */
    public static function canForceDelete(Model $record): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'force-delete-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if multiple priority sets can be restored.
     *
     * @return bool True if multiple priority sets can be restored, false otherwise.
     */
    public static function canRestoreMultiple(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'restore-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if multiple priority sets can be force deleted.
     *
     * @return bool True if multiple priority sets can be force deleted, false otherwise.
     */
    public static function canForceDeleteMultiple(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'force-delete-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    /**
     * Determine if the resource can be reordered.
     *
     * @return bool True if the resource can be reordered, false otherwise.
     */
    public static function canReorder(): bool
    {
        // Get an instance of the current model
        $model = static::getModel();
        // Format the model name for the permission
        $modelName = ModelUtility::getNameForPermission($model);
        $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'reorder-' . $modelName;

        // Check if the user has the permission to access the resource
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
