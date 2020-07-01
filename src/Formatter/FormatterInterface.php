<?php

namespace SportSpar\Grids\Formatter;

interface FormatterInterface
{
    /**
     * This function accepts a value and format it to preferably a string or "Stringable" value
     *
     * @param mixed $value
     *
     * @return string
     */
    public function format($value);
}