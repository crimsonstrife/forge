<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscordConfigResource\Pages;
use App\Filament\Resources\DiscordConfigResource\RelationManagers;
use App\Models\DiscordConfig;
use App\Settings\DiscordSettings;
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

/**
 * Class DiscordConfigResource
 *
 * This class represents the resource for managing Discord configuration settings.
 * It extends the Resource class and provides methods for form schema, table configuration,
 * defining relations, pages, and permissions related to Discord settings.
 */
class DiscordConfigResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'fab-discord';

    protected static string $settings = DiscordSettings::class;

    protected static ?string $slug = 'discord-settings';

    protected ?string $heading = 'Manage Discord Settings';

    protected static ?string $model = "DiscordConfig";

    protected ?string $subheading = 'Configure the Discord settings of the platform.';

    protected static ?string $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'Discord';

    /**
     * Define the form schema for the DiscordConfigResource.
     *
     * @param Forms\Form $form The form instance.
     * @return Forms\Form The configured form instance.
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Add the enabled toggle, required fields must be filled in before enabling
                Forms\Components\Toggle::make('enabled')
                    ->label('Enable Discord Connectivity')
                    ->default(false)
                    ->helperText('Enable or disable the Discord connectivity feature.'),

                // Add the fields to the form
                Forms\Components\TextInput::make('client_id')
                    ->label('Client ID')
                    ->required()
                    ->helperText('The Client ID of the Discord application.'),

                Forms\Components\TextInput::make('client_secret')
                    ->label('Client Secret')
                    ->password()  // Hide the value as a password field
                    ->dehydrated()  // Ensure it's saved as encrypted
                    ->required()
                    ->helperText('The Client Secret of the Discord application.'),

                Forms\Components\TextInput::make('bot_token')
                    ->label('Bot Token')
                    ->password()  // Hide the value as a password field
                    ->dehydrated()  // Ensure it's saved as encrypted
                    ->required()
                    ->helperText('The Bot Token of the Discord application.'),

                Forms\Components\TextInput::make('guild_id')
                    ->label('Guild ID (Server ID)')
                    ->required()
                    ->helperText('The Guild ID of the Discord server.'),

                Forms\Components\TextInput::make('redirect_uri')
                    ->label('Redirect URI')
                    ->required()
                    ->helperText('The Redirect URI of the Discord application.'),

                Forms\Components\Textarea::make('role_mappings')
                    ->label('Role Mappings')
                    ->json()
                    ->rows(5)
                    ->helperText('The mappings of roles between the platform and Discord.'),

                Forms\Components\Textarea::make('channel_mappings')
                    ->label('Channel Mappings')
                    ->json()
                    ->rows(5)
                    ->helperText('The mappings of channels between the platform and Discord.'),
            ]);
    }

    /**
     * Configure the table for the DiscordConfigResource.
     *
     * @param Tables\Table $table The table instance to configure.
     * @return Tables\Table The configured table instance.
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')->label('Client ID'),
                Tables\Columns\TextColumn::make('guild_id')->label('Guild ID'),
            ])
            ->filters([
                //
            ]);
    }

    /**
     * Get the relations for the DiscordConfigResource.
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
     * Get the pages associated with the DiscordConfigResource.
     *
     * @return array An array of pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscordConfig::route('/'),
            //'create' => Pages\CreateDiscordConfig::route('/create'),
            'edit' => Pages\EditDiscordConfig::route('/{record}/edit'),
        ];
    }

    /**
     * Get the model class name.
     *
     * @return string The model class name.
     */
    public static function getModel(): string
    {
        return DiscordConfig::class;
    }

    /**
     * Retrieve the Discord configuration settings.
     *
     * @return array The Discord configuration settings.
     */
    public static function getDiscordConfig()
    {
        return self::getModel()::first();
    }

    /**
     * Determine if the current user can access the Discord configuration resource.
     *
     * @return bool True if the user can access the resource, false otherwise.
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
     * Determine whether any Discord configuration can be viewed.
     *
     * @return bool True if any Discord configuration can be viewed, false otherwise.
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
     * Determine if a new Discord configuration can be created.
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
     * Determine if multiple Discord configurations can be restored.
     *
     * @return bool True if multiple configurations can be restored, false otherwise.
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
     * Determine if multiple Discord configurations can be force deleted.
     *
     * @return bool True if multiple configurations can be force deleted, false otherwise.
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
