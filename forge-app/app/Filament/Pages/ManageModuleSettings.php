<?php

namespace App\Filament\Pages;

use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Settings\ModuleSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ManageModuleSettings extends SettingsPage
{
    protected static bool $shouldRegisterNavigation = true;

    protected static string $settings = ModuleSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $slug = 'module-settings';

    protected ?string $heading = 'Manage Module Settings';

    protected ?string $subheading = 'Configure the module settings of the platform, enabling or disabling optional modules.';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Modules';

    /* public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'manage-module-settings' permission.
        $user = Auth::user();
        $permission = 'manage-module-settings';
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
                            Toggle::make('crucible_enabled')
                                ->label(__('Crucible'))
                                ->helperText(__('Enable or disable the Crucible module.')),

                            Toggle::make('jira_enabled')
                                ->label(__('Jira'))
                                ->helperText(__('Enable or disable the Jira module.')),

                            Toggle::make('gitea_enabled')
                                ->label(__('Gitea'))
                                ->helperText(__('Enable or disable the Gitea module.')),
                        ]),
                    Grid::make(3)
                        ->schema([
                            Toggle::make('slack_enabled')
                                ->label(__('Slack'))
                                ->helperText(__('Enable or disable the Slack module.')),

                            Toggle::make('discord_enabled')
                                ->label(__('Discord'))
                                ->helperText(__('Enable or disable the Discord module.')),
                        ]),
                ]),
        ];
    }
}
