<?php

namespace App\Http\Controllers;

use App\Models\SavedIssueView;
use App\Services\Issues\IssueExplorerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class IssueSavedViewController extends Controller
{
    public function __construct(
        private readonly IssueExplorerService $explorer,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'is_shared' => ['nullable', 'boolean'],
            'state' => ['required', 'string'],
        ]);

        $decoded = json_decode($data['state'], true);

        if (! is_array($decoded)) {
            return back()->withErrors(['name' => 'The saved view state was invalid.'])->withInput();
        }

        $state = $this->explorer->stateForStorage([
            'query' => (string) Arr::get($decoded, 'query', ''),
            'sort' => (string) Arr::get($decoded, 'sort', 'updated_desc'),
            'next' => (bool) Arr::get($decoded, 'next', false),
            'filters' => collect(IssueExplorerService::FILTER_KEYS)
                ->mapWithKeys(fn (string $key): array => [$key => (string) Arr::get($decoded, "filters.{$key}", '')])
                ->all(),
        ]);

        $view = SavedIssueView::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->current_team_id,
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(trim($data['name'])),
            'query' => $state['query'],
            'sort' => $state['sort'],
            'filters' => array_merge($state['filters'], ['next' => $state['next']]),
            'is_shared' => $request->boolean('is_shared') && filled($user->current_team_id),
        ]);

        return redirect()
            ->to(url('/issues?'.http_build_query(['view' => $view->slug])))
            ->with('status', 'Saved view created.');
    }

    public function destroy(Request $request, SavedIssueView $savedIssueView): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null && $savedIssueView->isOwnedBy($user), 403);

        $savedIssueView->delete();

        return redirect()
            ->to(url('/issues'))
            ->with('status', 'Saved view deleted.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'view';
        $slug = $base;
        $suffix = 2;

        while (SavedIssueView::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
