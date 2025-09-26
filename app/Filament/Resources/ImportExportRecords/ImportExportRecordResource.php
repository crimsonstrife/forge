<?php

namespace App\Filament\Resources\ImportExportRecords;

use App\Filament\Resources\ImportExportRecords\Pages\CreateImportExportRecord;
use App\Filament\Resources\ImportExportRecords\Pages\EditImportExportRecord;
use App\Filament\Resources\ImportExportRecords\Pages\ListImportExportRecords;
use App\Filament\Resources\ImportExportRecords\Pages\ViewImportExportRecord;
use App\Filament\Resources\ImportExportRecords\Schemas\ImportExportRecordForm;
use App\Filament\Resources\ImportExportRecords\Schemas\ImportExportRecordInfolist;
use App\Filament\Resources\ImportExportRecords\Tables\ImportExportRecordsTable;
use App\Models\ImportExportRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ImportExportRecordResource extends Resource
{
    protected static ?string $model = ImportExportRecord::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Projects';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsUpDown;

    public static function form(Schema $schema): Schema
    {
        return ImportExportRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ImportExportRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImportExportRecordsTable::configure($table);
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
            'index' => ListImportExportRecords::route('/'),
            'create' => CreateImportExportRecord::route('/create'),
            'view' => ViewImportExportRecord::route('/{record}'),
            'edit' => EditImportExportRecord::route('/{record}/edit'),
        ];
    }
}
