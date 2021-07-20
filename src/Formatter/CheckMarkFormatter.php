<?php

namespace SportSpar\Grids\Formatter;

class CheckMarkFormatter implements FormatterInterface
{
    /**
     * @var string
     */
    private $onTrue;

    /**
     * @var string
     */
    private $onFalse;

    /**
     * @param string $onTrue
     * @param string $onFalse
     */
    public function __construct($onTrue = '✓', $onFalse = '')
    {
        $this->onTrue = $onTrue;
        $this->onFalse = $onFalse;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value)
    {
        return (bool)$value ? $this->onTrue : $this->onFalse;
    }
}
