<?php

namespace SportSpar\Grids\Formatter;

use DateTime;
use Throwable;

class DateFormatter implements FormatterInterface
{
    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $onInvalidDate;

    /**
     * @param string $format
     * @param string $onInvalidDate
     */
    public function __construct($format = 'Y-m-d H:i:s', $onInvalidDate = '-')
    {
        $this->format        = $format;
        $this->onInvalidDate = $onInvalidDate;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value)
    {
        try {
            $date = new DateTime($value);
        } catch (Throwable $e) {
            return $this->onInvalidDate;
        }

        return $date->format($this->format);
    }
}
