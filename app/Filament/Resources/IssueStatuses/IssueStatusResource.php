<?php

namespace App\Filament\Resources\IssueStatuses;

use App\Filament\Resources\IssueStatuses\Pages\CreateIssueStatus;
use App\Filament\Resources\IssueStatuses\Pages\EditIssueStatus;
use App\Filament\Resources\IssueStatuses\Pages\ListIssueStatuses;
use App\Filament\Resources\IssueStatuses\Pages\ViewIssueStatus;
use App\Filament\Resources\IssueStatuses\Schemas\IssueStatusForm;
use App\Filament\Resources\IssueStatuses\Schemas\IssueStatusInfolist;
use App\Filament\Resources\IssueStatuses\Tables\IssueStatusesTable;
use App\Models\IssueStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IssueStatusResource extends Resource
{
    protected static ?string $model = IssueStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return IssueStatusForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IssueStatusInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IssueStatusesTable::configure($table);
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
            'index' => ListIssueStatuses::route('/'),
            'create' => CreateIssueStatus::route('/create'),
            'view' => ViewIssueStatus::route('/{record}'),
            'edit' => EditIssueStatus::route('/{record}/edit'),
        ];
    }
}
