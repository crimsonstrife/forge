<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Tests\TestCase;

class SyncRepositoryIssuesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_repo_sync_treats_advisory_text_as_success_when_status_is_ok(): void
    {
        $link = $this->makeLink('ok', 'Provider returned 0 issues. Closed-only repositories are treated as advisory.');

        Bus::shouldReceive('dispatchSync')->once()->andReturnNull();

        $exitCode = Artisan::call('repo:sync', ['--link' => $link->id]);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Note: Provider returned 0 issues.', $output);
        $this->assertStringContainsString('Sync complete.', $output);
    }

    public function test_repo_sync_still_fails_when_last_sync_status_is_error(): void
    {
        $link = $this->makeLink('error', 'The provider token has expired.');

        Bus::shouldReceive('dispatchSync')->once()->andReturnNull();

        $exitCode = Artisan::call('repo:sync', ['--link' => $link->id]);
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Error: The provider token has expired.', $output);
    }

    private function makeLink(string $status, ?string $message): ProjectRepository
    {
        $project = Project::factory()->create([
            'name' => 'Repo Sync '.Str::random(8),
            'key' => Str::upper(Str::random(4)),
        ]);

        $repository = Repository::query()->create([
            'provider' => 'github',
            'host' => 'github.com',
            'owner' => 'octo-org',
            'name' => 'repo-'.Str::lower(Str::random(8)),
        ]);

        return ProjectRepository::query()->create([
            'project_id' => $project->id,
            'repository_id' => $repository->id,
            'last_sync_status' => $status,
            'last_sync_error' => $message,
            'initial_import_started_at' => now()->subMinute(),
            'initial_import_finished_at' => now(),
        ]);
    }
}
