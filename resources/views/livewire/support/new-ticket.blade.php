<div>
    <form wire:submit.prevent="submit" class="vstack gap-3">
        <div>
            <label class="form-label">Product</label>
            <select class="form-select" wire:model.defer="productId" required>
                <option value="">— Select a product —</option>
                @foreach($products as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
            @error('productId') <div class="text-danger small">{{ $message }}</div> @enderror
            <div class="form-text">We’ll route your ticket to the right project based on the product.</div>
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
            <input type="text" wire:model.defer="subject" class="form-control">
            @error('subject') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label">Describe the problem</label>
            <textarea rows="6" wire:model.defer="body" class="form-control"></textarea>
            @error('body') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('support.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>
    </form>
</div>
