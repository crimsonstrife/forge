<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\Settings;
use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Settings\GiteaSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ManageGiteaSettings extends SettingsPage
{
    //protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'misc-gitea';

    protected static string $settings = GiteaSettings::class;

    protected static ?string $slug = 'gitea-settings';

    protected ?string $heading = 'Manage Gitea Settings';

    protected ?string $subheading = 'Configure the Gitea settings of the platform.';

    protected static ?string $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'Gitea';

    /**
     * Only show the Gitea settings page if the module is enabled in the module settings.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $moduleSettings = new \App\Settings\ModuleSettings();
        return $moduleSettings->gitea_enabled;
    }

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'manage-module-settings' permission.
        $user = Auth::user();
        $permission = 'manage-gitea-settings';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }
    } */

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Toggle::make('enabled')
                                ->label(__('Enabled'))
                                ->helperText(__('Enable or disable the Gitea module.')),

                            TextInput::make('base_url')
                                ->label(__('Base URL'))
                                ->placeholder(__('https://gitea.example.com'))
                                ->helperText(__('The base URL of the Gitea server instance.')),

                            TextInput::make('api_token')
                                ->label(__('API Token'))
                                ->placeholder(__('API token'))
                                ->helperText(__('The API token used to authenticate with the Gitea server instance.')),
                        ]),
                ]),
        ];
    }
}
