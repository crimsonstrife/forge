<div>
    <form wire:submit.prevent="submit" class="vstack gap-3">
        <div>
            <label class="form-label">Product</label>
            <select class="form-select" wire:model.live="productId" required>
                <option value="">— Select a product —</option>
                @foreach($products as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
            @error('productId') <div class="text-danger small">{{ $message }}</div> @enderror
            <div class="form-text">
                @if($selectedProduct)
                    Routed to {{ $selectedProduct['project'] ?? 'the product team' }}.
                    @if(!empty($selectedProduct['description'])) {{ $selectedProduct['description'] }} @endif
                @else
                    We’ll route your ticket to the right project based on the product.
                @endif
            </div>
        </div>

        <div>
            <label class="form-label">Type</label>
            <select class="form-select" wire:model.live="typeId">
                @foreach($types as $type)
                    <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                @endforeach
            </select>
            @error('typeId') <div class="text-danger small">{{ $message }}</div> @enderror
            <div class="form-text">{{ $typeTemplate['intro'] }}</div>
        </div>

        <div>
            <label class="form-label">Your name</label>
            <input type="text" wire:model.defer="name" class="form-control">
            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" wire:model.defer="email" class="form-control">
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label">Subject</label>
            <input type="text" wire:model.defer="subject" class="form-control" placeholder="{{ $typeTemplate['subject'] }}">
            @error('subject') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label">Describe the problem</label>
            <textarea rows="6" wire:model.defer="body" class="form-control" placeholder="{{ $typeTemplate['body'] }}"></textarea>
            @error('body') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>
</div>
