<?php

namespace App\Filament\Resources\IssueTypes;

use App\Filament\Resources\IssueTypes\Pages\CreateIssueType;
use App\Filament\Resources\IssueTypes\Pages\EditIssueType;
use App\Filament\Resources\IssueTypes\Pages\ListIssueTypes;
use App\Filament\Resources\IssueTypes\Pages\ViewIssueType;
use App\Filament\Resources\IssueTypes\Schemas\IssueTypeForm;
use App\Filament\Resources\IssueTypes\Schemas\IssueTypeInfolist;
use App\Filament\Resources\IssueTypes\Tables\IssueTypesTable;
use App\Models\IssueType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IssueTypeResource extends Resource
{
    protected static ?string $model = IssueType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return IssueTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IssueTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IssueTypesTable::configure($table);
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
            'index' => ListIssueTypes::route('/'),
            'create' => CreateIssueType::route('/create'),
            'view' => ViewIssueType::route('/{record}'),
            'edit' => EditIssueType::route('/{record}/edit'),
        ];
    }
}
