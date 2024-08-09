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

    /**
     * CrucibleService constructor.
     * @param CrucibleSettings $settings
     */
    public function __construct(CrucibleSettings $settings) // Constructor to initialize the CrucibleService with settings
    {
        $this->enabled = config('services.crucible.enabled'); // Get the 'enabled' configuration value from services.crucible
        $this->baseUrl = config('services.crucible.base_url'); // Get the 'base_url' configuration value from services.crucible
        $this->apiToken = config('services.crucible.api_token'); // Get the 'api_token' configuration value from services.crucible
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
     * Verify a repository URL.
     * @param mixed $url
     * @return mixed
     */
    public function verifyRepository($url) // Method to verify a repository URL
    {
        if (!$this->isEnabled()) { // Check if Crucible service is not enabled
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
}
