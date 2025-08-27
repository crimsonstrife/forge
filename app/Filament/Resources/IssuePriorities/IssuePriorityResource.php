<?php

namespace App\Filament\Resources\IssuePriorities;

use App\Filament\Resources\IssuePriorities\Pages\CreateIssuePriority;
use App\Filament\Resources\IssuePriorities\Pages\EditIssuePriority;
use App\Filament\Resources\IssuePriorities\Pages\ListIssuePriorities;
use App\Filament\Resources\IssuePriorities\Pages\ViewIssuePriority;
use App\Filament\Resources\IssuePriorities\Schemas\IssuePriorityForm;
use App\Filament\Resources\IssuePriorities\Schemas\IssuePriorityInfolist;
use App\Filament\Resources\IssuePriorities\Tables\IssuePrioritiesTable;
use App\Models\IssuePriority;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IssuePriorityResource extends Resource
{
    protected static ?string $model = IssuePriority::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return IssuePriorityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IssuePriorityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IssuePrioritiesTable::configure($table);
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
            'index' => ListIssuePriorities::route('/'),
            'create' => CreateIssuePriority::route('/create'),
            'view' => ViewIssuePriority::route('/{record}'),
            'edit' => EditIssuePriority::route('/{record}/edit'),
        ];
    }
}
