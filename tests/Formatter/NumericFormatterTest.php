<?php

namespace Formatter;

use SportSpar\Grids\Formatter\NumericFormatter;
use PHPUnit\Framework\TestCase;

class NumericFormatterTest extends TestCase
{
    public function testFormat()
    {
        $formatter = new NumericFormatter();
        $this->assertSame('123456789.12', $formatter->format(123456789.123456));

        $formatter = new NumericFormatter(2, ',', '.');
        $this->assertSame('123.456.789,12', $formatter->format(123456789.123456));
    }
}
