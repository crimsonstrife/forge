<x-app-layout>
    <x-slot name="header">
        <div class="h5 mb-0">New Milestone</div>
    </x-slot>

    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('projects.milestones.store', $project) }}">
                    @csrf

                    @include('projects.milestones._form', ['milestone' => null])

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Create</button>
                        <a class="btn btn-outline-secondary" href="{{ route('projects.milestones.index', $project) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
