<?php

namespace SportSpar\Grids\Formatter;

class LinkFormatter implements FormatterInterface
{
    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $linkClass;

    /**
     * @param string $link
     * @param string $linkClass
     */
    public function __construct($link = '%s', $linkClass = '')
    {
        $this->link      = $link;
        $this->linkClass = $linkClass;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value)
    {
        return sprintf(
            '<a class="%s" href="%s">%s</a>',
            $this->linkClass,
            sprintf($this->link, $value),
            $value
        );
    }
}
