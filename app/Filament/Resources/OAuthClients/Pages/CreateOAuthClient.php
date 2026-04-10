<?php

namespace App\Filament\Resources\OAuthClients\Pages;

use App\Filament\Resources\OAuthClients\OAuthClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOAuthClient extends CreateRecord
{
    protected static string $resource = OAuthClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['secret'] = Str::random(40);
        $data['personal_access_client'] = false;
        $data['password_client'] = false;
        $data['revoked'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        $secret = $this->record->plainSecret;

        if (! filled($secret)) {
            \Filament\Notifications\Notification::make()
                ->title('Client Created')
                ->body('The client was created, but the plaintext secret is no longer available. Rotate the secret to generate a new one.')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        \Filament\Notifications\Notification::make()
            ->title('Client Created — Copy Your Secret')
            ->body("Client Secret: {$secret}\n\nThis secret will not be shown again. Store it securely in your Codex FORGE_CLIENT_SECRET environment variable.")
            ->warning()
            ->persistent()
            ->send();
    }
}
