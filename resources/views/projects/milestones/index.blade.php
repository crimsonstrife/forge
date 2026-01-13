<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="me-auto">
                <div class="h5 mb-0">Milestones</div>
                <div class="text-muted small">{{ $project->name }}</div>
            </div>

            <a href="{{ route('projects.milestones.create', $project) }}" class="btn btn-primary">
                New
            </a>
        </div>
    </x-slot>

    <div class="container py-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="get" class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="Milestone name...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="all" @selected($filters['type'] === 'all')>All</option>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select">
                            <option value="all" @selected($filters['state'] === 'all')>All</option>
                            @foreach ($stateOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['state'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-outline-primary" type="submit">Apply</button>
                    <a class="btn btn-outline-secondary"
                       href="{{ route('projects.milestones.index', $project) }}">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>State</th>
                        <th>Due</th>
                        <th class="text-end">Issues</th>
                        <th class="text-end">Sprints</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($milestones as $milestone)
                        <tr>
                            @php
                                $typeValue = $milestone->type instanceof BackedEnum ? $milestone->type->value : (string) $milestone->type;
                                $stateValue = $milestone->state instanceof BackedEnum ? $milestone->state->value : (string) $milestone->state;
                            @endphp
                            <td class="fw-semibold">
                                {{ $milestone->name }}
                                @if ($typeValue === 'release' && $milestone->version)
                                    <span class="text-muted ms-2">{{ $milestone->version }}</span>
                                @endif
                            </td>
                            <td>{{ $typeOptions[$typeValue] ?? ucfirst($typeValue) }}</td>
                            <td>{{ $stateOptions[$stateValue] ?? ucfirst($stateValue) }}</td>
                            <td>{{ $milestone->due_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="text-end">{{ $milestone->issues_count }}</td>
                            <td class="text-end">{{ $milestone->sprints_count }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-primary"
                                       href="{{ route('projects.milestones.edit', [$project, $milestone]) }}">
                                        Edit
                                    </a>

                                    <form method="post"
                                          action="{{ route('projects.milestones.destroy', [$project, $milestone]) }}"
                                          onsubmit="return confirm('Delete this milestone?');">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No milestones found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-body border-top">
                {{ $milestones->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
