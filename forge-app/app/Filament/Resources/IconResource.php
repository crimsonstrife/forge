<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IconResource\Pages;
use App\Filament\Resources\IconResource\RelationManagers;
use App\Models\Icon;
use App\Models\User;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Services\SvgSanitizerService;
use Illuminate\Support\Facades\Log;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Illuminate\Filesystem\Filesystem;

/**
 * Class IconResource responsible for managing icon resources.
 *
 * This class extends from the base Resource class and provides configurations
 * and form definitions for managing icons within the application.
 */
class IconResource extends Resource
{
    protected static ?string $model = Icon::class;

    protected static ?string $navigationIcon = 'far-icons';

    protected static ?string $activeNavigationIcon = 'fas-icons';

    protected static ?string $navigationGroup = 'Application';

    protected static bool $softDelete = true;

    protected static bool $search = true;

    protected static ?array $styleArray = [
        'outline' => 'Outline',
        'solid' => 'Solid',
        'duotone' => 'Duotone',
        'brand' => 'Brand',
        'regular' => 'Regular',
        'light' => 'Light',
        'thin' => 'Thin',
        'custom' => 'Custom',
    ];

    // Define the protected types
    protected static ?array $typeArray = [
        'heroicon' => 'Heroicons',
        'octicon' => 'Octicons',
        'fontawesome' => 'Font Awesome',
        'misc' => 'Miscellaneous',
    ];

    protected static ?array $customTypes = ['custom' => 'Custom'];

    /**
     * Define the form schema for the IconResource.
     *
     * @param Form $form The form instance.
     * @return Form The configured form instance.
     */
    public static function form(Form $form): Form
    {
        $filesystem = new Filesystem();

        $storagePath = storage_path('app/public/icons');

        // Load the blade-icons configuration to get prefix mappings
        $bladeIcons = Config::get('blade-icons', []);

        // Get the sets from the blade-icons configuration
        $sets = $bladeIcons['sets'] ?? [];

        // Get the prefixes from the sets, and pair them with the set name/key
        $prefixes = collect($sets)->mapWithKeys(fn ($set, $key) => [$key => $set['prefix']]);


        // Get the classes from the sets, and pair them with the set name/key
        $classes = collect($sets)->mapWithKeys(fn ($set, $key) => [$key => $set['class']]);

        // Set static variables
        $typeArray = static::$typeArray;
        $customTypes = static::$customTypes;

        // Get only existing custom types (non-built-in)
        $existingTypes = Icon::query()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->filter(fn ($type) => !in_array($type, array_keys($typeArray)))
            ->mapWithKeys(fn ($type) => [$type => Str::title($type)])
            ->toArray();

        // Combine existing custom types with predefined custom types
        $availableTypes = array_merge($existingTypes, $customTypes);

        return $form
            ->schema([
                // Name of the icon
                Forms\Components\TextInput::make('name')
                    ->label('Icon Name')
                    ->required()
                    ->reactive()
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->helperText('Enter a unique name for the icon. This should be lowercase, with hyphens for spaces.')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', Str::slug($state, '-')))
                    ->rule(function ($get) {
                        $type = $get('type');
                        $style = $get('style');

                        // Ensure both type and style are set before applying the unique rule
                        return Rule::unique('icons', 'name')
                            ->where(function ($query) use ($type, $style) {
                                if ($type) {
                                    $query->where('type', $type);
                                }
                                if ($style) {
                                    $query->where('style', $style);
                                }
                            });
                    })
                    ->validationAttribute('name'),

                // Icon Style Selection
                Forms\Components\Select::make('style')
                    ->label('Icon Style')
                    ->options(static::$styleArray)
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->required()
                    ->reactive()
                    ->helperText('Choose a predefined style for the icon.')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $type = $get('type');  // Retrieve type field value
                        $set('set', ($type ?? '') . '-' . $state);
                    }),

                // Icon Type Selection
                Forms\Components\Select::make('type')
                    ->label('Icon Type')
                    ->options(fn () => is_array($availableTypes) ? $availableTypes : [])
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->searchable()
                    ->reactive()
                    ->helperText('Choose a predefined type for the icon.')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $style = $get('style');  // Retrieve style field value
                        $set('set', $state . '-' . ($style ?? ''));
                        // Show the 'New Icon Type' field only if no existing type is selected
                        if (!$state) {
                            $set('new_type', true);
                        }
                    }),

                // Hidden set field - automatically set based on the type and style, ie custom-solid or custom-outline
                Forms\Components\Hidden::make('set')
                    ->default(fn ($get) => $get('type') . '-' . $get('style'))
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set, $get) use ($prefixes, $classes) {
                        $prefix = $prefixes[$state] ?? 'custom-c';
                        $class = $classes[$state] ?? 'custom-icon-set custom-icon';
                        //Log::debug('Set: ' . $state);
                        $set('prefix', $prefix);
                        $set('class', $class);
                    })
                    ->afterStateUpdated(function ($state, callable $set, $get) use ($prefixes, $classes) {
                        $prefix = $prefixes[$state] ?? 'custom-c';
                        $class = $classes[$state] ?? 'custom-icon-set custom-icon';
                        //Log::debug('Set: ' . $state);
                        $set('prefix', $prefix);
                        $set('class', $class);
                    }),

                // Hidden Prefix Field - automatically set based on the type
                Forms\Components\Hidden::make('prefix')
                    ->default(fn ($get) => $prefixes[$get('set')] ?? 'custom-c')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive(),

                // Hidden Class Field - automatically set based on the type
                Forms\Components\Hidden::make('class')
                    ->default(fn ($get) => $classes[$get('set')] ?? 'custom-icon-set custom-icon')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive(),

                // Icon file upload
                Forms\Components\FileUpload::make('svg_file_path')
                    ->label('Upload SVG File')
                    ->disk('local')
                    ->directory(fn ($get) => $storagePath . "/custom/{$get('type')}/{$get('style')}") // Set the upload directory based on the type and style
                    ->default([]) // Ensures the state is always an array
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->helperText('Upload an SVG file. If provided, this file will take priority over SVG code.')
                    ->visible(fn ($get) => $get('type') === 'custom')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set, $get) {
                        if (is_string($state)) {
                            // Check if file exists on disk and wrap it as an array with URL if it exists
                            $fileUrl = Storage::disk('local')->exists($state) ? Storage::disk('local')->url($state) : null;
                            $set('svg_file_path', $fileUrl ? [$fileUrl] : []);
                        } elseif (is_array($state) && count($state) === 1) {
                            // If it’s already an array with one path, ensure the URL is accessible
                            $fileUrl = Storage::disk('local')->exists($state[0]) ? Storage::disk('local')->url($state[0]) : null;
                            $set('svg_file_path', $fileUrl ? [$fileUrl] : []);
                        }
                    })
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Ensure the state is wrapped in an array if it’s a single path
                        if (is_string($state)) {
                            $fileUrl = Storage::disk('local')->exists($state) ? Storage::disk('local')->url($state) : null;
                            $set('svg_file_path', $fileUrl ? [$fileUrl] : []);
                        }
                    }),

                // Custom SVG Code
                Forms\Components\Textarea::make('svg_code')
                    ->label('Custom SVG Code')
                    ->helperText('Paste SVG code here if no file is uploaded. SVG will be saved as a file upon submission.')
                    ->visible(fn ($get) => $get('type') === 'custom' && !$get('svg_file_path'))
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set) {
                        $sanitizer = app(SvgSanitizerService::class);
                        // Only sanitize the SVG code if it's not empty
                        if ($state) {
                            $sanitized = $sanitizer->sanitize($state);
                            $set('svg_code', $sanitized);
                        } else {
                            $set('svg_code', $state);
                        }
                        $set('preview_source', 'code'); // Set preview to code
                    }),

                // Live Preview
                Forms\Components\ViewField::make('preview')
                    ->label('Live Preview')
                    ->view('components.icon-preview')
                    ->extraAttributes(fn ($get) => [
                        'svg_code' => $get('preview_source') === 'code' ? $get('svg_code') : null,
                        'svg_file_path' => $get('preview_source') === 'file' ? $get('svg_file_path') : null,
                    ]),
            ]);
    }


    /**
     * Configure the table for the IconResource.
     *
     * @param Table $table The table instance to configure.
     * @return Table The configured table instance.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Icon Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('style')->label('Style')->sortable()->searchable(),
                Tables\Columns\ViewColumn::make('preview')
                    ->label('Preview')
                    ->view('components.icon-preview')
                    ->extraAttributes(fn ($record) => [
                        'selectedIconId' => $record->id, // Pass the ID to `icon-preview`
                    ])
                    ->sortable(false)
                    ->searchable(false),
            ])
            ->filters([
                // Filter by icon type and/or style - generated dynamically based on existing icons
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(function () {
                        return Icon::query()
                            ->select('type')
                            ->distinct()
                            ->pluck('type')
                            ->mapWithKeys(fn ($type) => [$type => Str::title($type)]);
                    })
                    ->placeholder('All Types'),

                Tables\Filters\SelectFilter::make('style')
                    ->label('Style')
                    ->options(function () {
                        return Icon::query()
                            ->select('style')
                            ->distinct()
                            ->pluck('style')
                            ->mapWithKeys(fn ($style) => [$style => Str::title($style)]);
                    })
                    ->placeholder('All Styles'),
            ]);
    }

    /**
     * Get the relations for the resource.
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
     * Get the pages associated with the IconResource.
     *
     * @return array An array of pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIcons::route('/'),
            'create' => Pages\CreateIcon::route('/create'),
            'edit' => Pages\EditIcon::route('/{record}/edit'),
        ];
    }

    /**
     * Determine if the current user can access the IconResource.
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
     * Determine if any icons can be viewed.
     *
     * @return bool True if any icons can be viewed, false otherwise.
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
     * Determine if the resource can be created.
     *
     * @return bool True if the resource can be created, false otherwise.
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
     * Determine whether the given record can be force deleted.
     *
     * @param Model $record The record to check.
     * @return bool True if the record can be force deleted, false otherwise.
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
     * Determine if multiple icons can be restored.
     *
     * @return bool True if multiple icons can be restored, false otherwise.
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
     * Determine if multiple records can be force deleted.
     *
     * @return bool True if multiple records can be force deleted, false otherwise.
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
     * Determine if the icons can be reordered.
     *
     * @return bool True if the icons can be reordered, false otherwise.
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
