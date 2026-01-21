@php
    $milestoneType = \App\Utilities\Helpers\CommonHelpers::getEnumValue($milestone?->type);
    $milestoneState = \App\Utilities\Helpers\CommonHelpers::getEnumValue($milestone?->state);

    $currentType = old('type', $milestoneType ?? 'milestone');
    $currentState = old('state', $milestoneState ?? 'planned');
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Name</label>
        <input name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $milestone->name ?? '') }}"
               required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" required>
            @foreach ($typeOptions as $value => $label)
                <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">State</label>
        <select name="state" class="form-select" required>
            @foreach ($stateOptions as $value => $label)
                <option value="{{ $value }}" @selected($currentState === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="3">{{ old('description', $milestone->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Start</label>
        <input type="datetime-local"
               name="starts_at"
               class="form-control @error('starts_at') is-invalid @enderror"
               value="{{ old('starts_at', optional($milestone->starts_at ?? null)?->format('Y-m-d\TH:i')) }}">
        @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Due</label>
        <input type="datetime-local"
               name="due_at"
               class="form-control @error('due_at') is-invalid @enderror"
               value="{{ old('due_at', optional($milestone->due_at ?? null)?->format('Y-m-d\TH:i')) }}">
        @error('due_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Version (Release)</label>
        <input name="version"
               class="form-control @error('version') is-invalid @enderror"
               value="{{ old('version', $milestone->version ?? '') }}"
               placeholder="v0.6.0">
        @error('version') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Required when Type = Release.</div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Released At</label>
        <input type="datetime-local"
               name="released_at"
               class="form-control @error('released_at') is-invalid @enderror"
               value="{{ old('released_at', optional($milestone->released_at ?? null)?->format('Y-m-d\TH:i')) }}">
        @error('released_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
