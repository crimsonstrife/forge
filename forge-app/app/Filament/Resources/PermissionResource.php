<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\User;
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

    protected static ?string $navigationIcon = 'heroicon-o-key';

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
        // Get the authenticated user and check if they have the 'list-permission' permission.
        $user = Auth::user();
        $permission = 'list-permission';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        // Get the authenticated user and check if they have the 'list-permission' permission.
        $user = Auth::user();
        $permission = 'list-permission';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'read-permission' permission.
        $authUser = Auth::user();
        $permission = 'read-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
            // TODO: Check if the authenticated user has the 'read-permission' permission for the given record.
        }

        return false;
    }

    public static function canCreate(): bool
    {
        // Get the authenticated user and check if they have the 'create-permission' permission.
        $user = Auth::user();
        $permission = 'create-permission';
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
        // Get the authenticated user and check if they have the 'update-permission' permission.
        $authUser = Auth::user();
        $permission = 'update-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'delete-permission' permission.
        $authUser = Auth::user();
        $permission = 'delete-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestore(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'restore-permission' permission.
        $authUser = Auth::user();
        $permission = 'restore-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-permission' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestoreMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'restore-permission' permission.
        $authUser = Auth::user();
        $permission = 'restore-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDeleteMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-permission' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canReorder(): bool
    {
        // Get the authenticated user and check if they have the 'reorder-permission' permission.
        $authUser = Auth::user();
        $permission = 'reorder-permission';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
