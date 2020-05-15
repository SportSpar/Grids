<?php

namespace SportSpar\Grids;

class ArrayDataRow extends DataRow
{
    /**
     * {@inheritdoc}
     */
    protected function extractCellValue($fieldName)
    {
        if (strpos($fieldName, '.') !== false) {
            $parts = explode('.', $fieldName);
            $res = $this->src;
            foreach ($parts as $part) {
                if (isset($res[$part])) {
                    $res = $res[$part];
                } else {
                    return $res;
                }
            }
            return $res;
        }

        if (isset($this->src[$fieldName])) {
            return $this->src[$fieldName];
        }

        return null;
    }
}
