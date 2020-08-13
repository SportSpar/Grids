<?php

namespace SportSpar\Grids\Formatter;


class StripTagsFormatter implements FormatterInterface
{

    /**
     * @inheritDoc
     */
    public function format($value)
    {
        return strip_tags($value);
    }
}
