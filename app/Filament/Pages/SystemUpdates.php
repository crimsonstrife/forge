<?php

namespace App\Filament\Pages;

use App\Services\SelfUpdateService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemUpdates extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedArrowPath;
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $title = 'System Updates';
    protected string $view = 'filament.pages.system-updates';

    public string $currentVersion = '0.1.0';
    public bool $updateAvailable = false;

    public function mount(SelfUpdateService $updates): void
    {
        $this->currentVersion  = $updates->currentVersion();
        $this->updateAvailable = $updates->isUpdateAvailable($this->currentVersion);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check')
                ->label('Check for Updates')
                ->icon(Heroicon::ArrowPath)
                ->color('gray')
                ->action(function (SelfUpdateService $updates): void {
                    $this->currentVersion  = $updates->currentVersion();
                    $this->updateAvailable = $updates->isUpdateAvailable($this->currentVersion);

                    if ($this->updateAvailable) {
                        Notification::make()
                            ->title('Update available.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('You are up to date.')
                            ->color('gray')
                            ->send();
                    }
                }),

            Action::make('updateNow')
                ->label('Update Now')
                ->icon(Heroicon::CloudArrowDown)
                ->color('primary')
                ->requiresConfirmation()
                ->disabled(fn (): bool => ! $this->updateAvailable)
                ->action(function (SelfUpdateService $updates): void {
                    $current = $updates->currentVersion();
                    $result  = $updates->runUpdate($current);

                    if (! $result['success']) {
                        Notification::make()
                            ->title($result['error'] ?? 'Update failed.')
                            ->danger()
                            ->send();

                        // Refresh state after failure
                        $this->currentVersion  = $updates->currentVersion();
                        $this->updateAvailable = $updates->isUpdateAvailable($this->currentVersion);
                        return;
                    }

                    // Success path
                    $new = $result['newVersion'] ?? $updates->currentVersion();

                    Notification::make()
                        ->title("Updated successfully to {$new}.")
                        ->success()
                        ->send();

                    $this->currentVersion  = $new;
                    $this->updateAvailable = false;
                }),
        ];
    }
}
