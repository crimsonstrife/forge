<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\CrucibleService;
use Illuminate\Support\Facades\Log;

class ProjectRepository extends Model
{
    use HasFactory;

    protected $fillable = [
        'remote_id',
        'name',
        'description',
        'slug',
        'http_url',
        'ssh_url',
        'scm_type',
        'main_branch',
        'project_id',
        'metadata',
        'history',
    ];

    protected $casts = [
        'metadata' => 'array',
        'history' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Verify the repository URL, and fetch the metadata if the connection is successful
     * @return bool true if the metadata is fetched successfully
     */
    public function verifyAndFetchMetadata()
    {
        $crucibleService = app(CrucibleService::class);

        if (!$crucibleService->isEnabled()) {
            Log::warning('Crucible connection is disabled or not configured properly.');
            return false;
        }

        if ($this->verify($this->http_url, $crucibleService)) {
            $result = $this->fetchMetaData($this->http_url, $crucibleService);

            if ($result) {
                // Log the success message if the metadata is fetched successfully
                Log::info('Metadata fetched successfully for repository', ['url' => $this->http_url]);
                return true;
            }

            // Log the error message if the metadata is not fetched successfully
            Log::error('Failed to fetch metadata for repository', ['url' => $this->http_url]);
            return false;
        }

        // Log the error message if the repository URL is not verified
        Log::error('Failed to verify repository', ['url' => $this->http_url]);
        return false;
    }

    /**
     * Update the metadata of the repository
     * @return bool true if the metadata is updated successfully
     */
    public function updateMetadata()
    {
        $crucibleService = app(CrucibleService::class);

        if (!$crucibleService->isEnabled()) {
            Log::warning('Crucible connection is disabled or not configured properly.');
            return false;
        }

        $data = $crucibleService->fetchRepositoryMetadata($this->url);

        if ($data) {
            $this->metadata = $data['metadata'] ?? [];
            $this->history = $data['history'] ?? [];
            $result = $this->save();

            if ($result) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Verify the repository URL, and return if the connection is successful and the repository exists
     * @param string $url
     * @param CrucibleService $service
     * @return bool
     */
    protected function verify(string $url, CrucibleService $service)
    {
        $crucibleService = $service;

        if (!$crucibleService->isEnabled()) {
            Log::warning('Crucible connection is disabled or not configured properly.');
            return false;
        }

        // Verify the repository URL using the Crucible service
        $data = $crucibleService->verifyRepository($url);

        // Log the response, and return true if the response is successful
        if ($data) {
            Log::info('Repository verified successfully', $data);
            return true;
        }

        // Log the error message, and return false if the response is not successful
        Log::error('Repository verification failed', ['url' => $url]);
        return false;
    }

    /**
     * Fetch the metadata of the repository
     * @param string $url
     * @param CrucibleService $service
     * @return bool true if the metadata is fetched successfully
     */
    protected function fetchMetaData(string $url, CrucibleService $service)
    {
        $crucibleService = $service;

        if (!$crucibleService->isEnabled()) {
            Log::warning('Crucible connection is disabled or not configured properly.');
            return false;
        }

        $data = $crucibleService->fetchRepositoryMetadata($url);

        if ($data) {
            $this->metadata = $data['metadata'] ?? [];
            $this->history = $data['history'] ?? [];
            $this->save();
            return true;
        }

        return false;
    }
}
