<?php

namespace library;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class CellReader
 * @package library
 */
class CellFilter implements IReadFilter {

    /**
     * @param string $column
     * @param int $row
     * @param string $worksheetName
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}
