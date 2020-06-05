<?php

namespace SportSpar\Grids\DataProvider\DataRow;

interface DataRowInterface
{
    /**
     * Returns row ID (row number).
     *
     * @return integer
     */
    public function getId();

    /**
     * Returns value of specified field.
     *
     * @param string $field
     *
     * @return mixed
     */
    public function getCellValue($field);
}
