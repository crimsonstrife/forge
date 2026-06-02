<?php

namespace App\Filament\Resources\FeedbackPosts\Pages;

use App\Filament\Resources\FeedbackPosts\FeedbackPostResource;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackPosts extends ListRecords
{
    protected static string $resource = FeedbackPostResource::class;
}
