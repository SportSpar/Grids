<?php

namespace Formatter;

use SportSpar\Grids\Formatter\DateFormatter;
use PHPUnit\Framework\TestCase;

class DateFormatterTest extends TestCase
{
    public function testFormatDefault()
    {
        $formatter = new DateFormatter();

        // Invalid date
        $this->assertSame('-', $formatter->format('wrong format'));

        $this->assertSame('2003-02-10 00:00:00', $formatter->format('10.02.2003'));

        // Empty date
        $this->assertSame('', $formatter->format(''));
    }

    public function testFormatCustom()
    {
        $formatter = new DateFormatter('Y-m-d', 'invalid', 'empty');

        // Invalid date
        $this->assertSame('invalid', $formatter->format('wrong format'));

        $this->assertSame('2003-02-10', $formatter->format('10.02.2003'));

        // Empty date
        $this->assertSame('empty', $formatter->format(''));
    }
}
