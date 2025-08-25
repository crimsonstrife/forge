<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GithubSettings;
use App\Settings\GiteaSettings;
use App\Settings\SyncSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class ConnectorsAndSyncSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|null|\UnitEnum $navigationGroup = 'Administration';
    protected static ?string $navigationLabel = 'Connectors & Sync';

    protected static ?string $slug = 'connectors-and-sync-settings';

    protected string $view = 'filament.pages.connectors-and-sync-settings';

    /** Bound form state */
    public ?array $data = [];

    public function mount(): void
    {
        $github = app(GithubSettings::class);
        $gitea  = app(GiteaSettings::class);
        $sync   = app(SyncSettings::class);

        $this->data = [
            'github_enabled' => $github->enabled,
            'github_app_name' => $github->app_name,
            'github_client_id' => $github->client_id,
            'github_api_base' => $github->api_base,
            'github_web_base' => $github->web_base,
            'gitea_enabled' => $gitea->enabled,
            'gitea_base_url' => $gitea->base_url,
            'gitea_app_name' => $gitea->app_name,
            'gitea_client_id' => $gitea->client_id,
            'sync_allow_outbound' => $sync->allow_outbound_issue_updates,
            'sync_auto_transition' => $sync->auto_transition_on_pr_merge,
            'sync_link_keyword_fix' => $sync->link_keyword_fix,
            'sync_link_keyword_close' => $sync->link_keyword_close,
        ];
    }

    /**
     * @throws \Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Grid::make(2)->schema([
                    Section::make('GitHub')->schema([
                        Toggle::make('github_enabled')->label('Enabled'),
                        TextInput::make('github_app_name')->label('App Name')->required(),
                        TextInput::make('github_client_id')->label('Client ID'),
                        TextInput::make('github_client_secret')->label('Client Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                        TextInput::make('github_webhook_secret')->label('Webhook Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                        TextInput::make('github_personal_access_token')->label('Personal Access Token')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                        TextInput::make('github_api_base')->label('API Base')->default('https://api.github.com')->required(),
                        TextInput::make('github_web_base')->label('Web Base')->default('https://github.com')->required(),
                    ])->columns(2),

                    Section::make('Gitea')->schema([
                        Toggle::make('gitea_enabled')->label('Enabled'),
                        TextInput::make('gitea_base_url')->label('Base URL')->placeholder('https://gitea.example.com')->required(),
                        TextInput::make('gitea_app_name')->label('App Name')->required(),
                        TextInput::make('gitea_client_id')->label('Client ID'),
                        TextInput::make('gitea_client_secret')->label('Client Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                        TextInput::make('gitea_webhook_secret')->label('Webhook Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                        TextInput::make('gitea_personal_access_token')->label('Personal Access Token')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s)),
                    ])->columns(2),

                    Section::make('Sync Policy')->schema([
                        Toggle::make('sync_allow_outbound')->label('Allow outbound issue updates'),
                        Toggle::make('sync_auto_transition')->label('Auto-transition on PR merge'),
                        TextInput::make('sync_link_keyword_fix')->label('Commit keyword for fix')->default('Fixes')->required(),
                        TextInput::make('sync_link_keyword_close')->label('Commit keyword for close')->default('Closes')->required(),
                    ])->columns(2),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Save')->color('primary')->action(fn () => $this->saveSettings()),
            Action::make('testGithub')->label('Test GitHub')
                ->visible(fn () => (bool) ($this->data['github_enabled'] ?? false))
                ->action(fn () => $this->testGithub()),
            Action::make('testGitea')->label('Test Gitea')
                ->visible(fn () => (bool) ($this->data['gitea_enabled'] ?? false))
                ->action(fn () => $this->testGitea()),
        ];
    }

    private function saveSettings(): void
    {
        $github = app(GithubSettings::class);
        $github->enabled = (bool) ($this->data['github_enabled'] ?? false);
        $github->app_name = (string) ($this->data['github_app_name'] ?? '');
        $github->client_id = (string) ($this->data['github_client_id'] ?? '');
        if (filled($this->data['github_client_secret'] ?? null)) {
            $github->client_secret = (string) $this->data['github_client_secret'];
        }
        if (filled($this->data['github_webhook_secret'] ?? null)) {
            $github->webhook_secret = (string) $this->data['github_webhook_secret'];
        }
        if (filled($this->data['github_personal_access_token'] ?? null)) {
            $github->personal_access_token = (string) $this->data['github_personal_access_token'];
        }
        $github->api_base = (string) ($this->data['github_api_base'] ?? 'https://api.github.com');
        $github->web_base = (string) ($this->data['github_web_base'] ?? 'https://github.com');
        $github->save();

        $gitea = app(GiteaSettings::class);
        $gitea->enabled = (bool) ($this->data['gitea_enabled'] ?? false);
        $gitea->base_url = (string) ($this->data['gitea_base_url'] ?? '');
        $gitea->app_name = (string) ($this->data['gitea_app_name'] ?? '');
        $gitea->client_id = (string) ($this->data['gitea_client_id'] ?? '');
        if (filled($this->data['gitea_client_secret'] ?? null)) {
            $gitea->client_secret = (string) $this->data['gitea_client_secret'];
        }
        if (filled($this->data['gitea_webhook_secret'] ?? null)) {
            $gitea->webhook_secret = (string) $this->data['gitea_webhook_secret'];
        }
        if (filled($this->data['gitea_personal_access_token'] ?? null)) {
            $gitea->personal_access_token = (string) $this->data['gitea_personal_access_token'];
        }
        $gitea->save();

        $sync = app(SyncSettings::class);
        $sync->allow_outbound_issue_updates = (bool) ($this->data['sync_allow_outbound'] ?? false);
        $sync->auto_transition_on_pr_merge = (bool) ($this->data['sync_auto_transition'] ?? true);
        $sync->link_keyword_fix = (string) ($this->data['sync_link_keyword_fix'] ?? 'Fixes');
        $sync->link_keyword_close = (string) ($this->data['sync_link_keyword_close'] ?? 'Closes');
        $sync->save();

        Notification::make()->title('Settings saved')->success()->send();
    }

    /**
     * @throws ConnectionException
     */
    private function testGithub(): void
    {
        $s = app(GithubSettings::class);

        if (! $s->enabled) {
            Notification::make()->title('GitHub disabled')->warning()->send();
            return;
        }

        if (! filled($s->personal_access_token)) {
            Notification::make()->title('Missing GitHub token')->danger()->send();
            return;
        }

        $resp = Http::withToken($s->personal_access_token)
            ->baseUrl($s->api_base)
            ->asJson()
            ->acceptJson()
            ->get('/user');

        $resp->successful()
            ? Notification::make()->title('GitHub OK: '.$resp->json('login'))->success()->send()
            : Notification::make()->title('GitHub failed')->body($resp->body())->danger()->send();
    }

    /**
     * @throws ConnectionException
     */
    private function testGitea(): void
    {
        $s = app(GiteaSettings::class);

        if (! $s->enabled) {
            Notification::make()->title('Gitea disabled')->warning()->send();
            return;
        }

        if (! filled($s->personal_access_token) || ! filled($s->base_url)) {
            Notification::make()->title('Missing Gitea token or base URL')->danger()->send();
            return;
        }

        $resp = Http::withToken($s->personal_access_token)
            ->baseUrl(rtrim($s->base_url, '/').'/api/v1')
            ->asJson()
            ->acceptJson()
            ->get('/user');

        $resp->successful()
            ? Notification::make()->title('Gitea OK: '.$resp->json('login'))->success()->send()
            : Notification::make()->title('Gitea failed')->body($resp->body())->danger()->send();
    }
}
