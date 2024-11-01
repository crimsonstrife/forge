<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssuePriorityResource\Pages;
use App\Filament\Resources\IssuePriorityResource\RelationManagers;
use App\Models\Issues\IssuePriority;
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
     * Update the selected icon preview.
     */
    public function updateIconPreview($iconId)
    {
        $this->selectedIconId = $iconId;
    }

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

                Forms\Components\TextInput::make('icon')
                    ->required()
                    ->label('Priority Icon (CSS class or URL)'),

                Forms\Components\Toggle::make('is_default')
                    ->label('Set as Default')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Priority Name'),
                Tables\Columns\TextColumn::make('color')->label('Priority Color'),
                Tables\Columns\IconColumn::make('is_default')->label('Default Priority')->boolean()->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuePriorities::route('/'),
            'create' => Pages\CreateIssuePriority::route('/create'),
            'edit' => Pages\EditIssuePriority::route('/{record}/edit'),
        ];
    }

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

    public static function canEdit(Model $record): bool
    {
        // alias for canUpdate
        return static::canUpdate($record);
    }

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
