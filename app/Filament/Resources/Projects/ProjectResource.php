<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Schemas\ProjectInfolist;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Exception;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    /**
     * @throws Exception
     */
    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    /**
     * @throws Exception
     */
    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\TeamsRelationManager::class,
            RelationManagers\IssueTypesRelationManager::class,
            RelationManagers\IssueStatusesRelationManager::class,
            RelationManagers\IssuePrioritiesRelationManager::class,
            RelationManagers\StatusTransitionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }

    //    public static function canAccess(): bool
    //    {
    //        $u = auth()->user();
    //        return ($u?->can('admin.panel.access') && $u?->can('projects.manage')) || $u?->can('is-super-admin');
    //    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name','key']; // adjust per model
    }
}
