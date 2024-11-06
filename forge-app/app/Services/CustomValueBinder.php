<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\DefaultValueBinder as BaseBinder;

/**
 * Class CustomValueBinder
 *
 * This class extends the DefaultValueBinder to provide custom value binding functionality.
 * It is used to bind values in a specific way that differs from the default implementation.
 * This helps provide compatibility between the PhpSpreadsheet library and the Maatwebsite/Laravel-Excel package.
 *
 * @package App\Services
 */
class CustomValueBinder extends BaseBinder
{
    /**
     * Bind a value to a cell.
     * This method is called by the PhpSpreadsheet library to bind a value to a cell.
     * It is overridden here to provide custom logic for binding the value and ensure compatibility with the Laravel-Excel package.
     *
     * @param Cell $cell
     * @param mixed $value
     * @return bool
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        // Instance of the PhpSpreadsheet library
        $defaultValueBinder = new DefaultValueBinder();

        // Bind the value to the cell using the default value binder
        return $defaultValueBinder->bindValue($cell, $value);
    }
}
