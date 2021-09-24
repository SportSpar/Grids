<?php

namespace SportSpar\Grids;

class SelectFilterConfig extends FilterConfig
{
    protected $template = '*.select';

    protected $options = [];

    protected $submitOnChange = false;

    protected $size = null;

    protected $multipleMode = false;

    /**
     * Returns option items of html select tag.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Sets option items for html select tag.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Returns true if form must be submitted immediately
     * when filter value selected.
     *
     * @return bool
     */
    public function isSubmittedOnChange(): bool
    {
        return $this->submitOnChange;
    }

    /**
     * Allows to submit form immediately when filter value selected.
     *
     * @param bool $isSubmittedOnChange
     *
     * @return self
     */
    public function setSubmittedOnChange(bool $isSubmittedOnChange): self
    {
        $this->submitOnChange = $isSubmittedOnChange;

        return $this;
    }

    /**
     * Sets the size of the select element.
     *
     * @param int $size
     *
     * @return self
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Returns the size of the select element.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Enabled multiple mode.
     * This will switch the selected operator to IN, as any other operator does not work with multiple selections.
     *
     * @param bool $multipleMode
     *
     * @return self
     */
    public function setMultipleMode(bool $multipleMode): self
    {
        $this->multipleMode = $multipleMode;

        if ($multipleMode) {
            $this->operator = FilterConfig::OPERATOR_IN;
        }

        return $this;
    }

    /**
     * Returns true if the multiple mode is enabled.
     *
     * @return bool
     */
    public function isMultipleMode(): bool
    {
        return $this->multipleMode;
    }
}
