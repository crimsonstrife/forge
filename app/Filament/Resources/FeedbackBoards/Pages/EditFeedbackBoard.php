<?php

namespace App\Filament\Resources\FeedbackBoards\Pages;

use App\Filament\Resources\FeedbackBoards\FeedbackBoardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeedbackBoard extends EditRecord
{
    protected static string $resource = FeedbackBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
