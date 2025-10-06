<div x-data="quickCapture()" x-init="init($root)" class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong>Quick Capture</strong>
        <small class="text-body-secondary">Press ⌘/Ctrl + K</small>
    </div>

    <div class="card-body">
        @if($this->projectOptions->isNotEmpty())
            <div class="mb-2">
                <label class="form-label">Project (for Convert to Issue)</label>
                <select class="form-select" wire:model="selectedProjectId">
                    <option value="">— Choose a project —</option>
                    @foreach($this->projectOptions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-2">
            <input type="text" class="form-control" placeholder="Title (optional)" wire:model.defer="title">
        </div>

        <div class="mb-2">
            <textarea class="form-control" rows="4" placeholder="Write a quick note…" wire:model.defer="body"></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" wire:click="save">Save Note</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="convertToIssue">
                Convert to Issue
            </button>
        </div>
    </div>

    @if($recent->isNotEmpty())
        <div class="list-group list-group-flush">
            @foreach($recent as $n)
                <div class="list-group-item small d-flex justify-content-between align-items-center">
                    <span class="text-truncate" style="max-width: 28rem;">
                        {{ $n->title ?? 'Untitled note' }}
                    </span>
                    @if ($n->issue)
                        <a class="text-decoration-none"
                           href="{{ route('issues.show', ['project' => $n->issue->project_id, 'issue' => $n->issue]) }}">
                            View issue ({{ $n->issue->key }})
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
          Alpine.data('quickCapture', () => ({
                root: null,
                init(root) {
                  this.root = root; // DOM root of THIS Livewire component instance
                  window.addEventListener('keydown', (e) => {
                        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                              e.preventDefault();
                              const ta = this.root.querySelector('textarea');
                              if (ta) ta.focus();
                            }
                      });
                },
          }));
        });
</script>
@endpush
