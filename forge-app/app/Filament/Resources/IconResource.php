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

/** @package App\Filament\Resources */
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

    public static function form(Form $form): Form
    {
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

                // Icon Type Selection
                Forms\Components\Select::make('type')
                    ->label('Icon Type')
                    ->options($availableTypes)
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

                // Hidden Prefix Field - automatically set based on the type
                Forms\Components\Hidden::make('prefix')
                    ->default(fn ($get) => $prefixes[$get('type')] ?? 'custom')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive(),

                // Hidden Class Field - automatically set based on the type
                Forms\Components\Hidden::make('class')
                    ->default(fn ($get) => $classes[$get('type')] ?? 'custom')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive(),

                // Hidden set field - automatically set based on the type and style, ie custom-solid or custom-outline
                Forms\Components\Hidden::make('set')
                    ->default(fn ($get) => $get('type') . '-' . $get('style'))
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->dehydrated()
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set, $get) use ($prefixes, $classes) {
                        // The keys in the $prefixes and $classes arrays are the same as the the set created here, so we can use the set to get the prefix and class
                        $thisSet = $get('set');
                        $prefix = $prefixes[$thisSet] ?? 'custom-c';
                        $class = $classes[$thisSet] ?? 'custom-icon-set custom-icon';

                        $set('prefix', $prefix);
                    }),

                // Icon Style
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

                // Icon file upload
                Forms\Components\FileUpload::make('svg_file_path')
                    ->label('Upload SVG File')
                    ->disk('public')
                    ->directory(fn ($get) => "uploads/icons/{$get('type')}/{$get('style')}")
                    ->afterStateHydrated(function ($state, $set, $get) {
                        // Set the file name to the slugified icon name + '.svg' if the file is new
                        if ($state instanceof \Livewire\TemporaryUploadedFile) {
                            $set('svg_file_path', Str::slug($get('name'), '-') . '.svg');
                        }

                        // Set the file path to the existing file path if the icon is being edited
                        if ($state === null && $get('svg_file_path')) {
                            $set('svg_file_path', $get('svg_file_path'));
                        }
                    })
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->helperText('Upload an SVG file. If provided, this file will take priority over SVG code.')
                    ->visible(fn ($get) => $get('type') === 'custom')
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('preview_source', 'file')),

                // Custom SVG Code
                Forms\Components\Textarea::make('svg_code')
                    ->label('Custom SVG Code')
                    ->helperText('Paste SVG code here if no file is uploaded. SVG will be saved as a file upon submission.')
                    ->visible(fn ($get) => $get('type') === 'custom' && !$get('svg_file_path'))
                    ->disabled(fn ($get) => $get('is_builtin')) // Disables the field if the icon is built-in
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $sanitizer = app(SvgSanitizerService::class);
                        $sanitized = $sanitizer->sanitize($state);
                        $set('svg_code', $sanitized);
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
     *
     * @param Table $table
     * @return Table
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws Exception
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIcons::route('/'),
            'create' => Pages\CreateIcon::route('/create'),
            'edit' => Pages\EditIcon::route('/{record}/edit'),
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
