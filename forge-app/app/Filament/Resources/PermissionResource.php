<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\User;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'far-shield-check';

    protected static ?string $activeNavigationIcon = 'fas-shield-check';

    protected static ?string $slug = 'permissions';

    protected ?string $heading = 'Manage Permissions';

    protected ?string $subheading = 'Permissions are used to control access to certain parts of the application.';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Permissions';

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'list-permission' permission.
        $user = Auth::user();
        $permission = 'list-permission';
        if ($user instanceof User) {
            $canDo = $user->hasPermissionTo($permission, 'web');

            if ($canDo) {
                return true;
            }

            return false;
        }

        return false;
    } */

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('guard_name')->sortable()->badge(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
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
