<?php

namespace App\Filament\Resources\FeedbackBoards\Pages;

use App\Filament\Resources\FeedbackBoards\FeedbackBoardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackBoards extends ListRecords
{
    protected static string $resource = FeedbackBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
