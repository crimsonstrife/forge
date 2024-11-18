<?php

namespace App\Services;

use Illuminate\Support\Facades\Http; // Import the Http facade for making HTTP requests
use Illuminate\Support\Facades\Log; // Import the Log facade for logging
use App\Settings\CrucibleSettings; // Import the CrucibleSettings class

/**
 * Class CrucibleService
 * @package App\Services
 * Service class to interact with a Crucible SCM service
 */
class CrucibleService
{
    protected $enabled; // Declare a protected property to store the enabled status
    protected $baseUrl; // Declare a protected property to store the base URL
    protected $apiToken; // Declare a protected property to store the API token
    protected $mockMode; // Declare a protected property to store the mock mode status

    /**
     * CrucibleService constructor.
     * @param CrucibleSettings $settings
     */
    public function __construct(CrucibleSettings $settings) // Constructor to initialize the CrucibleService with settings
    {
        $this->enabled = config('services.crucible.enabled'); // Get the 'enabled' configuration value from services.crucible
        $this->baseUrl = config('services.crucible.base_url'); // Get the 'base_url' configuration value from services.crucible
        $this->apiToken = config('services.crucible.api_token'); // Get the 'api_token' configuration value from services.crucible
        $this->mockMode = config('services.crucible.mock_mode'); // Get the 'mock_mode' configuration value from services.crucible
    }

    /**
     * Check if the Crucible service is enabled.
     * @return bool
     */
    public function isEnabled() // Method to check if the Crucible service is enabled
    {
        return $this->enabled && !empty($this->baseUrl) && !empty($this->apiToken); // Return true if enabled and base URL and API token are not empty
    }

    /**
     * Determine if the application is running in mock mode.
     *
     * @return bool True if the application is in mock mode, false otherwise.
     */
    public function isMockMode()
    {
        return $this->mockMode;
    }

    /**
     * Verify a repository URL.
     * @param mixed $url
     * @return mixed
     */
    public function verifyRepository($url) // Method to verify a repository URL
    {
        if (!$this->isEnabled()) { // Check if Crucible service is not enabled
            if ($this->isMockMode()) { // Check if the application is running in mock mode
                return $this->mockVerifyRepository($url); // Return the mock response
            }
            Log::warning('Crucible connection is disabled or not configured properly.'); // Log a warning message
            return null; // Return null to indicate that the verification cannot be performed
        }

        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories/verify", [ // Make an HTTP GET request with token
            'url' => $url, // Pass the repository URL as a query parameter
        ]);

        if ($response->successful()) { // Check if the request was successful
            return $response->json(); // Return the JSON response if successful
        }

        return null; // Return null if the request was not successful
    }

    /**
     * Fetch metadata for a repository URL.
     * @param mixed $url
     * @return mixed
     */
    public function fetchRepositoryMetadata($url) // Method to fetch metadata for a repository URL
    {
        if (!$this->isEnabled()) { // Check if Crucible service is not enabled
            if ($this->isMockMode()) { // Check if the application is running in mock mode
                return $this->mockFetchRepositoryMetadata($url); // Return the mock response
            }
            Log::warning('Crucible connection is disabled or not configured properly.'); // Log a warning message
            return null; // Return null to indicate that the metadata cannot be fetched
        }

        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories/metadata", [ // Make an HTTP GET request with token
            'url' => $url, // Pass the repository URL as a query parameter
        ]);

        if ($response->successful()) { // Check if the request was successful
            return $response->json(); // Return the JSON response if successful
        }

        return null; // Return null if the request was not successful
    }

    /**
     * Fetches the list of repositories from the Crucible service.
     *
     * @return array An array of repositories.
     */
    public function fetchRepositories()
    {
        if (!$this->isEnabled()) {
            if ($this->isMockMode()) {
                return $this->mockFetchRepositories();
            }
            Log::warning('Crucible connection is disabled or not configured properly.');
            return null;
        }

        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Fetches the history for a given repository.
     *
     * @param int $repositoryId The ID of the repository to fetch history for.
     * @return array The history data of the repository.
     */
    public function fetchHistory($repositoryId)
    {
        if (!$this->isEnabled()) {
            if ($this->isMockMode()) {
                return $this->mockFetchHistory($repositoryId);
            }
            Log::warning('Crucible connection is disabled or not configured properly.');
            return null;
        }

        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories/{$repositoryId}/history");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Mocks the verification of a repository given its URL.
     *
     * @param string $url The URL of the repository to verify.
     * @return void
     */
    private function mockVerifyRepository($url)
    {
        return [
            'verified' => true,
            'url' => $url,
            'message' => 'Mock: Repository verified successfully.',
        ];
    }


    /**
     * Mock method to fetch repository metadata from a given URL.
     *
     * @param string $url The URL of the repository to fetch metadata from.
     * @return array The mocked repository metadata.
     */
    private function mockFetchRepositoryMetadata($url)
    {
        return [
            'id' => 1,
            'url' => $url,
            'type' => 'git',
            'name' => 'Mock Repository',
        ];
    }


    /**
     * Mock method to fetch repositories.
     *
     * This method simulates the fetching of repositories for testing purposes.
     *
     * @return array An array of mock repository data.
     */
    private function mockFetchRepositories()
    {
        return [
            ['id' => 1, 'name' => 'Mock Repo 1', 'type' => 'git'],
            ['id' => 2, 'name' => 'Mock Repo 2', 'type' => 'svn'],
        ];
    }


    /**
     * Mock fetches the history for a given repository.
     *
     * @param int $repositoryId The ID of the repository to fetch history for.
     * @return array The mocked history data for the repository.
     */
    private function mockFetchHistory($repositoryId)
    {
        return [
            ['commit' => 'abc123', 'message' => 'Mock: Initial commit', 'date' => '2024-08-01'],
            ['commit' => 'def456', 'message' => 'Mock: Added feature X', 'date' => '2024-08-02'],
        ];
    }
}
