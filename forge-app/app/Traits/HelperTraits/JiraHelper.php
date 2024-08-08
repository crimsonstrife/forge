<?php

namespace App\Traits\HelperTraits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Provides functions to help connect to, and migrate from Jira instances.
 * @package App\Traits\HelperTraits
 */
trait JiraHelper
{
    /**
     * Connect to a Jira instance, return a Client object.
     * @param string $host
     * @param string $username
     * @param string $token
     * @throws GuzzleException
     * @return \GuzzleHttp\Client | null Returns a Client object if successful, null otherwise.
     */
    public function connectToJira(string $host, string $username, string $token): Client | null
    {
        // Create a new Guzzle client
        $client = new Client([
            'base_uri' => $host,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($username . ":" . $token)
            ]
        ]);

        // Test the connection, return the client if successful
        try {
            $response = $client->get('/rest/api/2/myself');
            if ($response->getStatusCode() === 200) {
                return $client;
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to connect to Jira: ' . $e->getMessage());
        }

        // Return null if the connection failed
        return null;
    }

    /**
     * Get all Projects from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @throws GuzzleException
     * @return array | null Returns an array of projects if successful, null otherwise.
     */
    public function getJiraProjects(Client $client): array | null
    {
        // Get all projects from the Jira instance
        try {
            $response = $client->get('/rest/api/2/project');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira projects: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issues from a Jira instance, by Project key.
     * @param \GuzzleHttp\Client $client
     * @param string | array $projectKey
     * @throws GuzzleException
     * @return array | null Returns an array of issues if successful, null otherwise.
     */
    public function getJiraIssues(Client $client, string | array $projectKeys): array | null
    {
        // Get all issues from the Jira instance
        try {
            // Format the issues for easier use
            $formatIssues = function ($issues) {
                $results = [];
                foreach ($issues as $issue) {
                    $results[] = [
                        'code' => $issue->key,
                        'name' => $issue->fields->summary,
                        'data' => $issue
                    ];
                }
                return $results;
            };

            // Define a placeholder array for the results
            $results = [];

            // Is the project key an array?
            if (is_array($projectKeys)) {
                // Loop through each project key
                foreach ($projectKeys as $projectKey) {
                    $response = $client->get('/rest/api/2/search?jql=project=' . $projectKey);
                    if ($response->getStatusCode() === 200) {
                        $data = json_decode($response->getBody()->getContents());
                        $results[$projectKey] = [
                            'total' => $data->total,
                            'issues' => $formatIssues($data->issues)
                        ];
                    }
                }
            } else {
                // Get all issues from the Jira instance
                $response = $client->get('/rest/api/2/search?jql=project=' . $projectKeys);
                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody()->getContents());
                    $results[$projectKeys] = [
                        'total' => $data->total,
                        'issues' => $formatIssues($data->issues)
                    ];
                }
            }

            return $results;
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issues: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issue Types from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @throws GuzzleException
     * @return array | null Returns an array of issue types if successful, null otherwise.
     */
    public function getJiraIssueTypes(Client $client): array | null
    {
        // Get all issue types from the Jira instance
        try {
            $response = $client->get('/rest/api/2/issuetype');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue types: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issue Statuses from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @throws GuzzleException
     * @return array | null Returns an array of issue statuses if successful, null otherwise.
     */
    public function getJiraIssueStatuses(Client $client): array | null
    {
        // Get all issue statuses from the Jira instance
        try {
            $response = $client->get('/rest/api/2/status');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue statuses: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issue Priorities from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @throws GuzzleException
     * @return array | null Returns an array of issue priorities if successful, null otherwise.
     */
    public function getJiraIssuePriorities(Client $client): array | null
    {
        // Get all issue priorities from the Jira instance
        try {
            $response = $client->get('/rest/api/2/priority');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue priorities: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issue Resolutions from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @throws GuzzleException
     * @return array | null Returns an array of issue resolutions if successful, null otherwise.
     */
    public function getJiraIssueResolutions(Client $client): array | null
    {
        // Get all issue resolutions from the Jira instance
        try {
            $response = $client->get('/rest/api/2/resolution');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue resolutions: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get details of a specific Issue from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @param string $issueKey
     * @throws GuzzleException
     * @return array | null Returns an array of issue details if successful, null otherwise.
     */
    public function getJiraIssueDetails(Client $client, string $issueKey): array | null
    {
        // Get details of a specific issue from the Jira instance
        try {
            $response = $client->get('/rest/api/2/issue/' . $issueKey);
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue details: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }

    /**
     * Get all Issue Comments from a Jira instance.
     * @param \GuzzleHttp\Client $client
     * @param string $issueKey
     * @throws GuzzleException
     * @return array | null Returns an array of issue comments if successful, null otherwise.
     */
    public function getJiraIssueComments(Client $client, string $issueKey): array | null
    {
        // Get all comments of a specific issue from the Jira instance
        try {
            $response = $client->get('/rest/api/2/issue/' . $issueKey . '/comment');
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to get Jira issue comments: ' . $e->getMessage());
        }

        // Return null if the request failed
        return null;
    }
}
