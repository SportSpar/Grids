<?php

namespace Formatter;

use SportSpar\Grids\Formatter\StripTagsFormatter;
use PHPUnit\Framework\TestCase;

class StripTagsFormatterTest extends TestCase
{

    public function testFormat()
    {
        $formatter = new StripTagsFormatter();
        $this->assertSame(
            'Hi Hello world',
            $formatter->format('<h1>Hi</h1> <br/><p>Hello world</p>'));
    }
}
