<?php

namespace App\Http\Controllers\Sentry;

use App\Models\IssuePriority;
use App\Models\IssueType;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class AlertRuleOptionsController extends Controller
{
    public function projects(Request $request): JsonResponse
    {
        $q = $this->query($request);

        $rows = Project::query()
            ->when($q !== '', fn ($b) => $b->where('name', 'like', '%'.$q.'%'))
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(static fn (Project $p): array => [(string) $p->id, (string) $p->name])
            ->all();

        return new JsonResponse(['choices' => $rows]);
    }

    public function issueTypes(Request $request): JsonResponse
    {
        $q = $this->query($request);

        $rows = IssueType::query()
            ->when($q !== '', fn ($b) => $b->where('name', 'like', '%'.$q.'%'))
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(static fn (IssueType $t): array => [(string) $t->id, (string) $t->name])
            ->all();

        return new JsonResponse(['choices' => $rows]);
    }

    public function priorities(Request $request): JsonResponse
    {
        $q = $this->query($request);

        $rows = IssuePriority::query()
            ->when($q !== '', fn ($b) => $b->where('name', 'like', '%'.$q.'%'))
            ->orderBy('order')
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(static fn (IssuePriority $p): array => [(string) $p->id, (string) $p->name])
            ->all();

        return new JsonResponse(['choices' => $rows]);
    }

    private function query(Request $request): string
    {
        return trim((string) ($request->query('query', '') ?: ''));
    }
}
