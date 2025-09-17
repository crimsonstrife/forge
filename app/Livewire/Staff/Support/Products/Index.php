<?php

namespace App\Livewire\Staff\Support\Products;

use App\Models\ServiceProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public function render(): View
    {
        $this->authorize('support.manage'); // gate/permission in your app

        $products = ServiceProduct::query()
            ->with('defaultProject:id,name')
            ->orderBy('name')
            ->get(['id','name','key','default_project_id','organization_id','updated_at']);

        return view('livewire.staff.support.products.index', compact('products'));
    }
}
