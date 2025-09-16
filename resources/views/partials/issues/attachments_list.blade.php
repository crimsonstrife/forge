<ul class="list-group list-group-flush">
    @forelse($attachments as $file)
        <li class="list-group-item d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span>📎</span>
                <div>
                    <div class="fw-semibold small">{{ $file->file_name }}</div>
                    <div class="text-body-secondary small">
                        {{ number_format($file->size / 1024, 1) }} KB · uploaded {{ $file->created_at?->diffForHumans() }}
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a class="small" href="{{ route('issues.attachments.download', [$project, $issue, $file]) }}">Download</a>

                @can('update', $issue)
                    <button
                        type="button"
                        class="btn btn-link btn-sm text-danger p-0"
                        x-on:click.stop="$dispatch('issue-attachment-delete', { id: {{ $file->id }} })"
                        title="{{ __('Delete attachment') }}"
                    >
                        {{ __('Delete') }}
                    </button>
                @endcan
            </div>
        </li>
    @empty
        <li class="list-group-item text-body-secondary small py-4">No attachments.</li>
    @endforelse
</ul>
