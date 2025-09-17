<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Products</div>
        <a class="btn btn-sm btn-primary" href="{{ route('support.staff.products.create') }}">New Product</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Key</th>
                <th>Default Project</th>
                <th>Updated</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td class="fw-semibold">{{ $p->name }}</td>
                    <td><code>{{ $p->key }}</code></td>
                    <td>{{ $p->defaultProject->name ?? '—' }}</td>
                    <td class="text-nowrap">{{ $p->updated_at?->diffForHumans() }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('support.staff.products.edit', ['productId' => $p->id]) }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-body-secondary">No products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
