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
     * @var string
     */
    private $onEmptyDate;

    public function __construct(string $format = 'Y-m-d H:i:s', string $onInvalidDate = '-', $onEmptyDate = '')
    {
        $this->format        = $format;
        $this->onInvalidDate = $onInvalidDate;
        $this->onEmptyDate   = $onEmptyDate;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value)
    {
        if (empty($value)) {
            return $this->onEmptyDate;
        }

        try {
            $date = new DateTime($value);
        } catch (Throwable $e) {
            return $this->onInvalidDate;
        }

        return $date->format($this->format);
    }
}
