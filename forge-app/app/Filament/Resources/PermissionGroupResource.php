<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionGroupResource\Pages;
use App\Filament\Resources\PermissionGroupResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionGroup;
use App\Models\Auth\PermissionSet;
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

class PermissionGroupResource extends Resource
{
    protected static ?string $model = PermissionGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $slug = 'permission-groups';

    protected ?string $heading = 'Manage Permission Groups';

    protected ?string $subheading = 'Permission groups are used to group permissions together.';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Permission Groups';

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'list-permission-group' permission.
        $user = Auth::user();
        $permission = 'list-permission-group';
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
                Select::make('permission_sets')
                    ->multiple()
                    ->relationship('permissionSets', 'name')
                    ->options(PermissionSet::all()->pluck('name', 'id'))
                    ->preload(),
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('permission_sets_count')->counts('permissionSets')->label('Permission Sets')->badge(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions')->badge(),
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
            'index' => Pages\ListPermissionGroups::route('/'),
            'create' => Pages\CreatePermissionGroup::route('/create'),
            'edit' => Pages\EditPermissionGroup::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        // Get the authenticated user and check if they have the 'list-permission-group' permission.
        $user = Auth::user();
        $permission = 'list-permission-group';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        // Get the authenticated user and check if they have the 'list-permission-group' permission.
        $user = Auth::user();
        $permission = 'list-permission-group';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'read-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'read-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
            // TODO: Check if the authenticated user has the 'read-permission-group' permission for the given record.
        }

        return false;
    }

    public static function canCreate(): bool
    {
        // Get the authenticated user and check if they have the 'create-permission-group' permission.
        $user = Auth::user();
        $permission = 'create-permission-group';
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
        // Get the authenticated user and check if they have the 'update-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'update-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'delete-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'delete-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestore(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'restore-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'restore-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestoreMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'restore-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'restore-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDeleteMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canReorder(): bool
    {
        // Get the authenticated user and check if they have the 'reorder-permission-group' permission.
        $authUser = Auth::user();
        $permission = 'reorder-permission-group';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
