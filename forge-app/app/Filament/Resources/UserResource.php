<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Auth\Role;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $slug = 'users';

    protected ?string $heading = 'Manage Users';

    protected ?string $subheading = 'Users are the people who use the application.';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Users';

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'list-user' permission.
        $user = Auth::user();
        $permission = 'list-user';
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
                TextInput::make('username')->required()->maxLength(255)->unique(),
                TextInput::make('email')->required()->email()->maxLength(255)->unique(),
                TextInput::make('first_name')->required()->maxLength(255),
                TextInput::make('last_name')->required()->maxLength(255),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('username')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')->label('Roles')->sortable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        // Get the authenticated user and check if they have the 'list-user' permission.
        $user = Auth::user();
        $permission = 'list-user';
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
        // Get the authenticated user and check if they have the 'list-user' permission.
        $user = Auth::user();
        $permission = 'list-user';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'read-user' permission.
        $authUser = Auth::user();
        $permission = 'read-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
            // TODO: Check if the authenticated user has the 'read-user' permission for the given record.
        }

        return false;
    }

    public static function canCreate(): bool
    {
        // Get the authenticated user and check if they have the 'create-user' permission.
        $user = Auth::user();
        $permission = 'create-user';
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
        // Get the authenticated user and check if they have the 'update-user' permission.
        $authUser = Auth::user();
        $permission = 'update-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'delete-user' permission.
        $authUser = Auth::user();
        $permission = 'delete-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestore(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'restore-user' permission.
        $authUser = Auth::user();
        $permission = 'restore-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-user' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canRestoreMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'restore-user' permission.
        $authUser = Auth::user();
        $permission = 'restore-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canForceDeleteMultiple(): bool
    {
        // Get the authenticated user and check if they have the 'force-delete-user' permission.
        $authUser = Auth::user();
        $permission = 'force-delete-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }

    public static function canReorder(): bool
    {
        // Get the authenticated user and check if they have the 'reorder-user' permission.
        $authUser = Auth::user();
        $permission = 'reorder-user';
        if ($authUser instanceof User) {
            return $authUser->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
