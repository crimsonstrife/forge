<?php

namespace App\Filament\Resources\FeedbackPosts\Pages;

use App\Filament\Resources\FeedbackPosts\FeedbackPostResource;
use App\Services\Feedback\MarkdownRenderer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeedbackPost extends EditRecord
{
    protected static string $resource = FeedbackPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['body'])) {
            $data['body_html'] = app(MarkdownRenderer::class)->render((string) $data['body']);
        }

        return $data;
    }
}
