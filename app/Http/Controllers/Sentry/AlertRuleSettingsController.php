<?php

namespace App\Http\Controllers\Sentry;

use App\Models\IssuePriority;
use App\Models\IssueType;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class AlertRuleSettingsController extends Controller
{
    public function store(Request $request): JsonResponse|Response
    {
        $projectId = $this->setting($request, 'forge_project_id');
        $issueTypeId = $this->setting($request, 'forge_issue_type_id');
        $priorityId = $this->setting($request, 'forge_priority_id');

        if ($projectId === '' || ! Project::query()->whereKey($projectId)->exists()) {
            return $this->error('Choose a valid Forge project.');
        }

        if ($issueTypeId === '' || ! IssueType::query()->whereKey($issueTypeId)->exists()) {
            return $this->error('Choose a valid issue type.');
        }

        if ($priorityId === '' || ! IssuePriority::query()->whereKey($priorityId)->exists()) {
            return $this->error('Choose a valid priority.');
        }

        return response()->noContent();
    }

    private function setting(Request $request, string $name): string
    {
        $direct = $request->input($name);
        if (is_scalar($direct)) {
            return trim((string) $direct);
        }

        $settings = $request->input('settings', []);
        if (is_array($settings)) {
            foreach ($settings as $setting) {
                if (! is_array($setting) || (string) ($setting['name'] ?? '') !== $name) {
                    continue;
                }

                $value = $setting['value'] ?? '';

                return is_scalar($value) ? trim((string) $value) : '';
            }
        }

        return '';
    }

    private function error(string $message): JsonResponse
    {
        return new JsonResponse(['message' => $message], 400);
    }
}
