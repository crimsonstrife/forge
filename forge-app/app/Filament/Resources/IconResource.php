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
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class IconResource extends Resource
{
    protected static ?string $model = Icon::class;

    protected static ?string $navigationIcon = 'far-icons';

    protected static ?string $activeNavigationIcon = 'fas-icons';

    protected static ?string $navigationGroup = 'Application';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Icon Name')
                    ->required()
                    ->reactive()
                    // Ensure the name is unique compared to existing icons of the same type and style
                    ->unique(static function ($query, $name, $state) {
                        return $query->where('name', $name)
                            ->where('type', $state['type'])->where('style', $state['style']);
                    })
                    ->helperText('Enter a unique name for the icon. This should be lowercase, with hyphens for spaces.')
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Sanitize the icon name to be lowercase and HTML-friendly
                        $set('name', Str::slug($state, '-'));
                    }),

                Forms\Components\Select::make('type')
                    ->label('Icon Type')
                    ->options(function () {
                        // Fetch existing custom icon types, excluding heroicon, octicons, and font awesome. If custom is not found, add it to the list.
                        return Icon::query()
                            ->select('type')
                            ->whereNotIn('type', ['heroicon', 'octicon', 'fontawesome'])
                            ->distinct()
                            ->pluck('type')
                            ->mapWithKeys(fn ($type) => [$type => Str::title($type)])
                            ->prepend('Custom', 'custom');
                    })
                    ->reactive()
                    ->searchable()
                    ->nullable()
                    ->helperText('Select from existing custom types or enter a new one.')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!$state) {
                            // If no existing type is selected, allow entry of a new type
                            $set('new_type', true);
                        }
                    }),

                // A text input for entering a new icon type if no existing type is selected
                Forms\Components\TextInput::make('new_type')
                    ->label('New Icon Type')
                    ->visible(fn ($get) => $get('type') === null || !in_array($get('type'), ['fontawesome', 'heroicon', 'octicon', 'custom'])) // Only show if no existing type is selected, or if the type is a custom one i.e not an included one from the base app.
                    ->required(fn ($get) => $get('type') === null || !in_array($get('type'), ['fontawesome', 'heroicon', 'octicon', 'custom'])) // Required if no existing type is selected
                    ->helperText('Enter a new type for the icon. This should be lowercase, with hyphens for spaces.')
                    ->unique(static function ($query, $type, $state) {
                        return $query->where('type', $type);
                    })
                    ->placeholder('my-new-type')
                    ->rules(['required_if:type,null', 'visible_if:type,null'])
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Sanitize the new type to be lowercase and HTML-friendly
                        $set('type', Str::slug($state, '-'));
                    })
                    ->dehydrated(), // Save only if filled in

                // SVG file upload and code fields remain the same as before
                Forms\Components\FileUpload::make('svg_file_path')
                    ->label('Upload SVG File')
                    ->disk('public')
                    ->directory('icons')
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->helperText('Upload an SVG file.')
                    ->visible(fn ($get) => $get('type') === 'custom')
                    ->dehydrated(fn ($get, $state) => $get('type') === 'custom' && !empty($state)),

                Forms\Components\Textarea::make('svg_code')
                    ->label('Custom SVG Code')
                    ->helperText('Paste the SVG code here for custom icons.')
                    ->visible(fn ($get) => $get('type') === 'custom' && !$get('svg_file_path'))
                    ->dehydrated(fn ($get, $state) => $get('type') === 'custom' && !empty($state))
                    // Required if no SVG file is uploaded and the icon type is custom
                    ->rules(['required_if:type,custom', 'required_if:svg_file_path,null'])
                    ->afterStateUpdated(function ($state, callable $set) {
                        $sanitizer = app(SvgSanitizerService::class);

                        // Sanitize the SVG code before saving
                        $sanitized = $sanitizer->sanitize($state);
                        $set('svg_code', $sanitized);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Icon Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('style')->label('Style')->sortable()->searchable(),
                Tables\Columns\ViewColumn::make('preview')
                ->label('Preview')
                ->view('components.icon-preview'), // Reference to a Blade component that renders the icon preview
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
