<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IconResource\Pages;
use App\Filament\Resources\IconResource\RelationManagers;
use App\Models\Icon;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        // Fetch existing custom icon types, excluding heroicon, octicons, and font awesome
                        return Icon::whereNotIn('type', ['heroicon', 'fontawesome', 'octicons'])
                            ->pluck('type', 'type')
                            ->mapWithKeys(fn ($type) => [Str::slug($type, '-') => $type]);
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
                    ->visible(fn ($get) => $get('type') === null) // Only show if no existing type is selected
                    ->required(fn ($get) => $get('type') === null) // Required if no existing type is selected
                    ->helperText('Enter a new type for the icon. This should be lowercase, with hyphens for spaces.')
                    ->unique(static function ($query, $type, $state) {
                        return $query->where('type', $type);
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Sanitize the new type to be lowercase and HTML-friendly
                        $set('type', Str::slug($state, '-'));
                    })
                    ->dehydrated(true), // Save only if filled in

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
                Tables\Columns\TextColumn::make('type')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('svg_file_path')
                    ->label('Uploaded SVG')
                    // Display the SVG file path if it exists and is accessible
                    ->visible(fn ($record) => $record && $record->svg_file_path !== null),
                Tables\Columns\TextColumn::make('svg_code')
                    ->label('Custom SVG Code')
                    ->limit(50)
                    ->visible(fn ($record) => $record && $record->svg_code !== null),
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
}
