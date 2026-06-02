<?php

namespace App\Filament\Resources\FeedbackBoards\Pages;

use App\Filament\Resources\FeedbackBoards\FeedbackBoardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedbackBoard extends CreateRecord
{
    protected static string $resource = FeedbackBoardResource::class;
}
