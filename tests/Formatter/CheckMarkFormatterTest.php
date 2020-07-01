<?php

namespace Formatter;

use SportSpar\Grids\Formatter\CheckMarkFormatter;
use PHPUnit\Framework\TestCase;

class CheckMarkFormatterTest extends TestCase
{
    public function testFormat()
    {
        $formatter = new CheckMarkFormatter('Yes', 'No');

        $this->assertSame('Yes', $formatter->format(true));
        $this->assertSame('Yes', $formatter->format(1));
        $this->assertSame('No', $formatter->format(false));
        $this->assertSame('No', $formatter->format(0));
    }
}
