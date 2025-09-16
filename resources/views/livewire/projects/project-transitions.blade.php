<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                <tr>
                    <th class="text-start">{{ __('From \\ To') }}</th>
                    @foreach($statuses as $to)
                        <th class="text-start">{{ $to->name }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($statuses as $from)
                    <tr>
                        <th class="text-start">{{ $from->name }}</th>
                        @foreach($statuses as $to)
                            <td>
                                @if($from->id === $to->id)
                                    <span class="small text-body-secondary">—</span>
                                @else
                                    @php $k = $from->id . ':' . $to->id; @endphp
                                    <input class="form-check-input" type="checkbox" wire:model.defer="matrix.{{ $k }}">
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('projects.show', ['project'=>$project]) }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
            <wa-button variant="brand" wire:click="save">{{ __('Save') }}</wa-button>
        </div>
    </div>
</div>
