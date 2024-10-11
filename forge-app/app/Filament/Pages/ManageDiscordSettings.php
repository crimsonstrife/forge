<?php

namespace App\Filament\Pages;

use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ManageDiscordSettings extends SettingsPage
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static string $settings = GiteaSettings::class;

    protected static ?string $slug = 'gitea-settings';

    protected ?string $heading = 'Manage Gitea Settings';

    protected ?string $subheading = 'Configure the Gitea settings of the platform.';

    protected static ?string $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'Gitea';

    protected function getFormSchema(): array
    {
        return [];
    }
}
