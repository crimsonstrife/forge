<?php

namespace App\Filament\Resources\OAuthClients\Pages;

use App\Filament\Resources\OAuthClients\OAuthClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOAuthClients extends ListRecords
{
    protected static string $resource = OAuthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    // After creation, show the secret once
                    \Filament\Notifications\Notification::make()
                        ->title('Client Created')
                        ->body("Client Secret: {$record->secret}\n\nCopy this now — it will not be shown again.")
                        ->warning()
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
