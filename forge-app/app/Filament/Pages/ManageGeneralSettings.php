<?php

namespace App\Filament\Pages;

use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ManageGeneralSettings extends SettingsPage
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $slug = 'general-settings';

    protected ?string $heading = 'Manage General Settings';

    protected ?string $subheading = 'Configure the general settings of the platform.';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'General';

    public static function shouldRegisterNavigation(): bool
    {
        // Get the authenticated user and check if they have the 'manage-general-settings' permission.
        $user = Auth::user();
        $permission = 'manage-general-settings';
        if ($user instanceof User) {
            return $user->hasPermissionTo($permission, 'web');
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            FileUpload::make('site_logo')
                                ->label(__('Site logo'))
                                ->helperText(__('This is the platform logo (e.g. Used in site favicon)'))
                                ->image()
                                ->columnSpan(1)
                                ->maxSize(config('system.max_file_size')),

                            Grid::make(1)
                                ->columnSpan(2)
                                ->schema([
                                    TextInput::make('site_name')
                                        ->label(__('Site name'))
                                        ->helperText(__('This is the platform name'))
                                        ->default(fn () => config('app.name'))
                                        ->required(),

                                    TextInput::make('site_description')
                                        ->label(__('Site description'))
                                        ->helperText(__('This is the platform description'))
                                        ->default(fn () => config('app.description')),

                                    Toggle::make('enable_registration')
                                        ->label(__('Enable registration?'))
                                        ->helperText(__('If enabled, any user can create an account in this platform. But an administration need to give them permissions.')),

                                    Toggle::make('enable_social_login')
                                        ->label(__('Enable social login?'))
                                        ->helperText(__('If enabled, configured users can login via their social accounts.')),

                                    Toggle::make('enable_login_form')
                                        ->label(__('Enable form login?'))
                                        ->helperText(__('If enabled, a login form will be visible on the login page.')),

                                    Toggle::make('enable_oidc_login')
                                        ->label(__('Enable OIDC login?'))
                                        ->helperText(__('If enabled, an OIDC Connect button will be visible on the login page.')),

                                    Select::make('site_language')
                                        ->label(__('Site language'))
                                        ->helperText(__('The language used by the platform.'))
                                        ->searchable(),

                                    Select::make('default_role')
                                        ->label(__('Default role'))
                                        ->helperText(__('The platform default role (used mainly in OIDC Connect).'))
                                        ->searchable()
                                        ->options(Role::all()->pluck('name', 'id')->toArray()),
                                ]),
                        ]),
                ]),
        ];
    }
}
