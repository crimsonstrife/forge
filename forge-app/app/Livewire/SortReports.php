<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Report;

/**
 * Class SortReports
 *
 * This Livewire component is responsible for handling the sorting of reports.
 * It extends the base Component class provided by Livewire.
 *
 * @package App\Livewire
 */
class SortReports extends Component
{
    public $reports;

    /**
     * Mount the component with the given reports.
     *
     * @param array $reports The reports to be sorted.
     * @return void
     */
    public function mount($reports)
    {
        $this->reports = $reports;
    }

    /**
     * Updates the order of reports based on the provided ordered IDs.
     *
     * @param array $orderedIds An array of report IDs in the new order.
     * @return void
     */
    public function updateOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Report::where('id', $id)->update(['order' => $index + 1]);
        }
    }

    /**
     * Render the view for sorting reports.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.sort-reports');
    }
}
