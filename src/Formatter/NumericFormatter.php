<?php

namespace SportSpar\Grids\Formatter;

class NumericFormatter implements FormatterInterface
{
    /**
     * @var int
     */
    private $decimals;

    /**
     * @var string
     */
    private $decimalSep;

    /**
     * @var string
     */
    private $thousandsSep;

    /**
     * @param int    $decimals
     * @param string $decimalSep
     * @param string $thousandsSep
     */
    public function __construct($decimals = 2, $decimalSep = '.', $thousandsSep = '')
    {
        $this->decimals = $decimals;
        $this->decimalSep = $decimalSep;
        $this->thousandsSep = $thousandsSep;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value)
    {
        return number_format($value, $this->decimals, $this->decimalSep, $this->thousandsSep);
    }
}
