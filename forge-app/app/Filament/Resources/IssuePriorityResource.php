<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssuePriorityResource\Pages;
use App\Filament\Resources\IssuePriorityResource\RelationManagers;
use App\Models\Issues\IssuePriority;
use App\Models\Issues\IssueType;
use App\Models\User;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Issues;
use App\Forms\Components\IconPicker;

/**
 * Class IssuePriorityResource
 *
 * This class represents the resource for managing issue priorities within the application.
 * It extends the base Resource class provided by the framework.
 *
 * @package App\Filament\Resources
 */
class IssuePriorityResource extends Resource
{
    protected static ?string $model = IssuePriority::class;

    protected static ?string $navigationIcon = 'far-arrow-down-short-wide';

    protected static ?string $activeNavigationIcon = 'fas-arrow-down-short-wide';

    protected static ?string $navigationGroup = 'Issues';

    protected static ?string $cluster = Issues::class;

    public $selectedIconId;

    protected $listeners = ['iconUpdated' => 'updateIconPreview'];


    /**
     * Updates the icon preview based on the provided icon ID.
     *
     * @param int $iconId The ID of the icon to update the preview for.
     * @return void
     */
    public function updateIconPreview($iconId)
    {
        $this->selectedIconId = $iconId;
    }

    /**
     * Define the form schema for the IssuePriorityResource.
     *
     * @param Form $form The form instance.
     * @return Form The configured form instance.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Priority Name'),

                Forms\Components\ColorPicker::make('color')
                    ->required()
                    ->label('Priority Color'),

                IconPicker::make('icon')
                    ->label('Select Icon')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        return <<<JS
                            if (window.Livewire) {
                                Livewire.emit('iconUpdated', {$state});
                            }
                        JS;
                    }),

                Forms\Components\Toggle::make('is_default')
                    ->label('Set as Default')
                    ->reactive()
                    ->helperText('If selected, this type will be the default for new issues globally.')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // When "is_default" is toggled to true, update other types to ensure only one default type
                        if ($state) {
                            IssueType::where('id', '<>', $get('id'))
                                ->update(['is_default' => false]);
                        }
                    }),
            ]);
    }

    /**
     * Configure the table for the IssuePriorityResource.
     *
     * @param Table $table The table instance to configure.
     * @return Table The configured table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            // Eager load the icon relationship to avoid N+1 query issues
            ->query(static::getModel()::with('icon')) // Explicitly setting the query
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Type Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                Tables\Columns\ViewColumn::make('preview')
                    ->label('Preview')
                    ->view('components.icon-preview')
                    ->extraAttributes(function ($record) {
                        // Add dd() to inspect $record->icon
                        dd($record->icon);
                        return [
                            'icon' => $record->icon()->first()?->id, // Retrieve the icon ID directly
                            'selectedIconId' => $record->icon()->first()?->id, // Retrieve the icon ID directly
                        ];
                    })
                    ->sortable(false)
                    ->searchable(false),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
            ]);
    }

    /**
     * Get the relations for the IssuePriority resource.
     *
     * @return array The array of relations.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages associated with the IssuePriorityResource.
     *
     * @return array An array of pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuePriorities::route('/'),
            'create' => Pages\CreateIssuePriority::route('/create'),
            'edit' => Pages\EditIssuePriority::route('/{record}/edit'),
        ];
    }

    /**
     * Determine if the current user can access the IssuePriorityResource.
     *
     * @return bool True if the user can access, false otherwise.
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
     * Determine whether any issue priorities can be viewed.
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
     * Determine if a new issue priority can be created.
     *
     * @return bool True if a new issue priority can be created, false otherwise.
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
     * Determine if multiple issue priorities can be restored.
     *
     * @return bool True if multiple issue priorities can be restored, false otherwise.
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
     * Determine if multiple issue priorities can be force deleted.
     *
     * @return bool True if multiple issue priorities can be force deleted, false otherwise.
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
     * Determine if the issue priorities can be reordered.
     *
     * @return bool True if the issue priorities can be reordered, false otherwise.
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
