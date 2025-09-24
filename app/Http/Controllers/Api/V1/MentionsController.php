<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

final class MentionsController extends Controller
{
    /**
     * Users autocompleter.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());
        $limit = max(1, min((int) $request->integer('limit', 8), 20));

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->when($q !== '', static function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(static function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $hasUserShow = RouteFacade::has('users.show');

        $data = $users->map(static function (User $u) use ($hasUserShow): array {
            $url = $hasUserShow ? route('users.show', ['user' => $u]) : url('/users/' . $u->id);

            return [
                'id'       => (string) $u->id,
                'label'    => $u->name,
                'sublabel' => (string) $u->email,
                'url'      => $url,
            ];
        })->all();

        return response()->json($data);
    }

    /**
     * Issues autocompleter.
     * Accepts optional ?project_id=… to scope results.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function issues(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());
        $limit = max(1, min((int) $request->integer('limit', 8), 20));
        $projectId = $request->string('project_id')->toString();

        $issues = Issue::query()
            ->select(['id', 'key', 'summary', 'project_id'])
            ->with(['project:id,key'])
            ->when($projectId !== '', static fn ($q2) => $q2->where('project_id', $projectId))
            ->when($q !== '', static function ($query) use ($q) {
                $needle = ltrim($q, '#'); // allow typing "#OMP-12"
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $needle) . '%';

                $query->where(static function ($w) use ($needle, $like) {
                    $w->where('key', 'like', $like)
                        ->orWhere('summary', 'like', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        $data = $issues->map(static function (Issue $i): array {
            // Route uses project + issue; Issue uses route key 'key'
            $url = route('issues.show', ['project' => $i->project, 'issue' => $i]);

            return [
                'id'       => (string) $i->id,
                // label should be what appears after "#" in the editor (e.g. OMP-6)
                'label'    => $i->key,
                // sublabel is helpful context in the dropdown
                'sublabel' => (string) $i->summary,
                'url'      => $url,
            ];
        })->all();

        return response()->json($data);
    }
}
