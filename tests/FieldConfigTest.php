<?php

use PHPUnit\Framework\TestCase;
use SportSpar\Grids\DataProvider\DataRow\ArrayDataRow;
use SportSpar\Grids\FieldConfig;
use SportSpar\Grids\Formatter\CheckMarkFormatter;

class FieldConfigTest extends TestCase
{
    public function testValueWillBeFormatted()
    {
        $dataRow = new ArrayDataRow(['column' => 'value'], 1);
        $field   = new FieldConfig('column');

        // No formatter
        $this->assertSame('value', $field->getValue($dataRow));

        // Format value from the row
        $field->setFormatter(new CheckMarkFormatter());
        $this->assertSame('✓', $field->getValue($dataRow));

        // Format value from the callback
        $field->setCallback(function() {
            return false;
        });
        $this->assertSame('', $field->getValue($dataRow));
    }
}
