<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Auth\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionSet;
use App\Models\Auth\PermissionGroup;
use App\Models\User;
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

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'roles';

    protected ?string $heading = 'Manage Roles';

    protected ?string $subheading = 'Roles are used to assign permissions to users.';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Roles';

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'list-role' permission.
        $user = Auth::user();
        $permission = 'list-role';
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
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permission_sets')
                    ->multiple()
                    ->relationship('permissionSets', 'name')
                    ->options(PermissionSet::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permission_groups')
                    ->multiple()
                    ->relationship('permissionGroups', 'name')
                    ->options(PermissionGroup::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions')->badge(),
                TextColumn::make('permission_sets_count')->counts('permissionSets')->label('Permission Sets')->badge(),
                TextColumn::make('permission_groups_count')->counts('permissionGroups')->label('Permission Groups')->badge(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        // Get the authenticated user and check if they have the 'list-role' permission.
        $user = Auth::user();
        $permission = 'list-role';
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
        // Get the authenticated user and check if they have the 'list-role' permission.
        $user = Auth::user();
        $permission = 'list-role';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'read-role' permission.
        $authUser = Auth::user();
        $permission = 'read-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
            // TODO: Check if the authenticated user has the 'read-role' permission for the given record.
        }

        return false;
    }

    public static function canCreate(): bool
    {
        // Get the authenticated user and check if they have the 'create-role' permission.
        $user = Auth::user();
        $permission = 'create-role';
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
        // Get the authenticated user and check if they have the 'update-role' permission.
        $authUser = Auth::user();
        $permission = 'update-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'delete-role' permission.
        $authUser = Auth::user();
        $permission = 'delete-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestore(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'restore-role' permission.
        $authUser = Auth::user();
        $permission = 'restore-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-role' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestoreMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'restore-role' permission.
        $authUser = Auth::user();
        $permission = 'restore-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDeleteMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-role' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canReorder(): bool
    {
        // Get the authenticated user and check if they have the 'reorder-role' permission.
        $authUser = Auth::user();
        $permission = 'reorder-role';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
