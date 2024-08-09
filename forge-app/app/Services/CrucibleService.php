<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CrucibleService
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('services.crucible.base_url');
        $this->apiToken = config('services.crucible.api_token');
    }

    public function verifyRepository($url)
    {
        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories/verify", [
            'url' => $url,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function fetchRepositoryMetadata($url)
    {
        $response = Http::withToken($this->apiToken)->get("{$this->baseUrl}/api/repositories/metadata", [
            'url' => $url,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
