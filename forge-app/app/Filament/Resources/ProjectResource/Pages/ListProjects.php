<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class ListProjects
 *
 * This class represents a list projects page.
 */
class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    /**
     * Retrieves a list of actions.
     *
     * @return array List of action objects.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Generates a query builder instance for retrieving table data
     * filtered by the authenticated user's ownership or association.
     *
     * @return Builder Query builder instance with applied filters.
     */
    protected function getTableQuery(): Builder
    {
        return parent::tableQuery()
            ->where(function ($query) {
                return $query->where('owner_id', Auth::user()->id)
                    ->orWhereHas('users', function ($query) {
                        return $query->where('users.id', Auth::user()->id);
                    });
            });
    }
}
