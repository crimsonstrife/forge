<?php

namespace App\Filament\Pages;

use App\Integrations\Sentry\Services\SentryClient;
use App\Models\IssuePriority;
use App\Models\IssueType;
use App\Models\Project;
use App\Settings\GiteaSettings;
use App\Settings\GithubSettings;
use App\Settings\SentrySettings;
use App\Settings\SyncSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
        $this->refreshData();
    }

    private function refreshData(): void
    {
        $github = app(GithubSettings::class);
        $gitea = app(GiteaSettings::class);
        $sync = app(SyncSettings::class);
        $sentry = app(SentrySettings::class);

        $this->data = [
            'github_enabled' => $github->enabled,
            'github_app_name' => $github->app_name,
            'github_client_id' => $github->client_id,
            'github_api_base' => $github->api_base,
            'github_web_base' => $github->web_base,
            'github_client_secret_set' => filled($github->client_secret),
            'github_webhook_secret_set' => filled($github->webhook_secret),
            'github_personal_access_token_set' => filled($github->personal_access_token),
            'gitea_enabled' => $gitea->enabled,
            'gitea_base_url' => $gitea->base_url,
            'gitea_app_name' => $gitea->app_name,
            'gitea_client_id' => $gitea->client_id,
            'gitea_client_secret_set' => filled($gitea->client_secret),
            'gitea_webhook_secret_set' => filled($gitea->webhook_secret),
            'gitea_personal_access_token_set' => filled($gitea->personal_access_token),
            'sync_allow_outbound' => $sync->allow_outbound_issue_updates,
            'sync_auto_transition' => $sync->auto_transition_on_pr_merge,
            'sync_link_keyword_fix' => $sync->link_keyword_fix,
            'sync_link_keyword_close' => $sync->link_keyword_close,
            'sentry_enabled' => $sentry->enabled,
            'sentry_org_slug' => $sentry->org_slug,
            'sentry_client_id' => $sentry->client_id,
            'sentry_api_base' => $sentry->api_base,
            'sentry_default_project_id' => $sentry->default_project_id,
            'sentry_default_issue_type_id' => $sentry->default_issue_type_id,
            'sentry_default_priority_id' => $sentry->default_priority_id,
            'sentry_installation_uuid' => $sentry->installation_uuid,
            'sentry_client_secret_set' => filled($sentry->client_secret),
            'sentry_auth_token_set' => filled($sentry->auth_token),
        ];
    }

    private function storedBadge(string $dataKey): string
    {
        return ! empty($this->data[$dataKey])
            ? 'Configured (leave blank to keep current value)'
            : 'Not set';
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
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('github_client_secret_set')),
                        TextInput::make('github_webhook_secret')->label('Webhook Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('github_webhook_secret_set')),
                        TextInput::make('github_personal_access_token')->label('Personal Access Token')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('github_personal_access_token_set')),
                        TextInput::make('github_api_base')->label('API Base')->default('https://api.github.com')->required(),
                        TextInput::make('github_web_base')->label('Web Base')->default('https://github.com')->required(),
                    ])->columns(2),

                    Section::make('Gitea')->schema([
                        Toggle::make('gitea_enabled')->label('Enabled'),
                        TextInput::make('gitea_base_url')->label('Base URL')->placeholder('https://gitea.example.com')->required(),
                        TextInput::make('gitea_app_name')->label('App Name')->required(),
                        TextInput::make('gitea_client_id')->label('Client ID'),
                        TextInput::make('gitea_client_secret')->label('Client Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('gitea_client_secret_set')),
                        TextInput::make('gitea_webhook_secret')->label('Webhook Secret')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('gitea_webhook_secret_set')),
                        TextInput::make('gitea_personal_access_token')->label('Personal Access Token')->password()->revealable()
                            ->dehydrated(fn ($s) => filled($s))
                            ->helperText(fn () => $this->storedBadge('gitea_personal_access_token_set')),
                    ])->columns(2),

                    Section::make('Sync Policy')->schema([
                        Toggle::make('sync_allow_outbound')->label('Allow outbound issue updates'),
                        Toggle::make('sync_auto_transition')->label('Auto-transition on PR merge'),
                        TextInput::make('sync_link_keyword_fix')->label('Commit keyword for fix')->default('Fixes')->required(),
                        TextInput::make('sync_link_keyword_close')->label('Commit keyword for close')->default('Closes')->required(),
                    ])->columns(2),

                    Section::make('Sentry')
                        ->description('Internal Sentry integration. Create the integration in your Sentry org, then paste the Client ID, Client Secret, and Auth Token here. See resources/integrations/sentry/schema.json for the UI schema to paste into Sentry.')
                        ->schema([
                            Toggle::make('sentry_enabled')->label('Enabled'),
                            TextInput::make('sentry_org_slug')->label('Sentry org slug')->placeholder('acme'),
                            TextInput::make('sentry_client_id')->label('Client ID'),
                            TextInput::make('sentry_client_secret')->label('Client Secret')->password()->revealable()
                                ->dehydrated(fn ($s) => filled($s))
                                ->helperText(fn () => $this->storedBadge('sentry_client_secret_set')),
                            TextInput::make('sentry_auth_token')->label('Auth Token')->password()->revealable()
                                ->dehydrated(fn ($s) => filled($s))
                                ->helperText(fn () => $this->storedBadge('sentry_auth_token_set')),
                            TextInput::make('sentry_api_base')->label('API Base')->default('https://sentry.io/api/0')->required(),
                            Select::make('sentry_default_project_id')->label('Default Forge project')
                                ->options(fn () => Project::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()->preload()->nullable(),
                            Select::make('sentry_default_issue_type_id')->label('Default issue type')
                                ->options(fn () => IssueType::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()->preload()->nullable(),
                            Select::make('sentry_default_priority_id')->label('Default priority')
                                ->options(fn () => IssuePriority::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()->preload()->nullable(),
                            Placeholder::make('sentry_installation_uuid_display')->label('Installation UUID')
                                ->content(fn () => $this->data['sentry_installation_uuid'] ?? 'Not yet captured'),
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
            Action::make('testSentry')->label('Test Sentry')
                ->visible(fn () => (bool) ($this->data['sentry_enabled'] ?? false))
                ->action(fn () => $this->testSentry()),
            Action::make('createSentryAlertRule')
                ->label('Create Sentry Rule')
                ->visible(fn () => (bool) ($this->data['sentry_enabled'] ?? false))
                ->schema([
                    TextInput::make('sentry_project_slug')
                        ->label('Sentry project slug')
                        ->required(),
                    TextInput::make('rule_name')
                        ->label('Rule name')
                        ->default('Send new issues to Forge')
                        ->required(),
                    TextInput::make('frequency')
                        ->label('Action frequency minutes')
                        ->numeric()
                        ->minValue(5)
                        ->maxValue(43200)
                        ->default(5)
                        ->required(),
                    Select::make('forge_project_id')
                        ->label('Forge project')
                        ->options(fn () => Project::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->default(fn () => $this->data['sentry_default_project_id'] ?? null)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('forge_issue_type_id')
                        ->label('Issue type')
                        ->options(fn () => IssueType::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->default(fn () => $this->data['sentry_default_issue_type_id'] ?? null)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('forge_priority_id')
                        ->label('Priority')
                        ->options(fn () => IssuePriority::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->default(fn () => $this->data['sentry_default_priority_id'] ?? null)
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->createSentryAlertRule($data);
                }),
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

        $sentry = app(SentrySettings::class);
        $sentry->enabled = (bool) ($this->data['sentry_enabled'] ?? false);
        $sentry->org_slug = (string) ($this->data['sentry_org_slug'] ?? '');
        $sentry->client_id = (string) ($this->data['sentry_client_id'] ?? '');
        if (filled($this->data['sentry_client_secret'] ?? null)) {
            $sentry->client_secret = (string) $this->data['sentry_client_secret'];
        }
        if (filled($this->data['sentry_auth_token'] ?? null)) {
            $sentry->auth_token = (string) $this->data['sentry_auth_token'];
        }
        $sentry->api_base = (string) ($this->data['sentry_api_base'] ?? 'https://sentry.io/api/0');
        $sentry->default_project_id = $this->data['sentry_default_project_id'] ?: null;
        $sentry->default_issue_type_id = $this->data['sentry_default_issue_type_id'] ? (int) $this->data['sentry_default_issue_type_id'] : null;
        $sentry->default_priority_id = $this->data['sentry_default_priority_id'] ? (int) $this->data['sentry_default_priority_id'] : null;
        $sentry->save();

        $this->refreshData();

        Notification::make()->title('Settings saved')->success()->send();
    }

    private function testSentry(): void
    {
        $client = app(SentryClient::class);

        if (! $client->isConfigured()) {
            Notification::make()->title('Sentry not configured')->warning()->send();

            return;
        }

        try {
            $client->ping()
                ? Notification::make()->title('Sentry OK')->success()->send()
                : Notification::make()->title('Sentry ping failed')->danger()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Sentry error')->body($e->getMessage())->danger()->send();
        }
    }

    /**
     * @param  array<string,mixed>  $data
     */
    private function createSentryAlertRule(array $data): void
    {
        try {
            $rule = app(SentryClient::class)->createIssueAlertRule(
                sentryProjectSlug: trim((string) ($data['sentry_project_slug'] ?? '')),
                ruleName: trim((string) ($data['rule_name'] ?? 'Send new issues to Forge')),
                forgeProjectId: (string) ($data['forge_project_id'] ?? ''),
                issueTypeId: (int) ($data['forge_issue_type_id'] ?? 0),
                priorityId: (int) ($data['forge_priority_id'] ?? 0),
                frequency: (int) ($data['frequency'] ?? 5),
            );

            $ruleId = (string) ($rule['id'] ?? '');
            Notification::make()
                ->title('Sentry rule created')
                ->body($ruleId !== '' ? 'Rule ID: '.$ruleId : 'Sentry accepted the rule.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Could not create Sentry rule')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
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
