<?php

namespace Formatter;

use PHPUnit\Framework\TestCase;
use SportSpar\Grids\Formatter\LinkFormatter;

class LinkFormatterTest extends TestCase
{
    public function testFormat()
    {
        $formatter = new LinkFormatter();
        $this->assertSame('<a class="" href="value">value</a>', $formatter->format('value'));

        $formatter = new LinkFormatter('http://www.site.com/%s', 'btn btn-primary');
        $this->assertSame('<a class="btn btn-primary" href="http://www.site.com/value">value</a>', $formatter->format('value'));
    }
}
