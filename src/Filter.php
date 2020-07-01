<?php

namespace SportSpar\Grids;

use View;

class Filter
{
    /**
     * @var FilterConfig
     */
    protected $config;

    /**
     * @var FieldConfig
     */
    protected $column;

    /**
     * Constructor.
     *
     * @param FilterConfig $config
     * @param FieldConfig  $column
     * @param Grid         $grid
     */
    public function __construct(
        FilterConfig $config,
        FieldConfig $column,
        Grid $grid
    ) {
        $this->config = $config;
        $this->column = $column;
        $this->grid = $grid;
    }

    /**
     * Returns input name for the filter.
     *
     * @return string
     */
    public function getInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();
        $name = $this->config->getId();

        return "{$key}[filters][{$name}]";
    }

    /**
     * Returns filter configuration.
     *
     * @return FilterConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns filters value.
     *
     * @return mixed
     */
    public function getValue()
    {
        $from_input = $this
            ->grid
            ->getInputProcessor()
            ->getFilterValue($this->config->getId());
        if ($from_input === null) {
            return $this->config->getDefaultValue();
        }

        return $from_input;
    }

    /**
     * Renders filtering control.
     *
     * @return string
     */
    public function render()
    {
        $data = $this->grid->getViewData();
        $data['column'] = $this->column;
        $data['filter'] = $this;
        $data['label'] = $this->config->getLabel();

        return View::make(
            $this->getTemplate(),
            $data
        )->render();
    }

    /**
     * Returns name of template for filtering control.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $filter_tpl = $this->config->getTemplate();
        $grid_tpl = $this->grid->getConfig()->getTemplate();

        return str_replace('*.', "$grid_tpl.filters.", $filter_tpl);
    }

    /**
     * Applies filtering to data source.
     */
    public function apply()
    {
        $value = (string)$this->getValue();

        if (null === $value || '' === $value) {
            return;
        }

        // If a filtering function is defined - use it
        if ($func = $this->config->getFilteringFunc()) {
            $func($value, $this->grid->getConfig()->getDataProvider());

            return;
        }

        // Check is value contains an operator, set this new operator and strip it from value
        // If operator is IN then value will be an array
        $value = $this->processValue($value);

        $isLike = $this->config->getOperator() === FilterConfig::OPERATOR_LIKE;
        if ($isLike && strpos($value, '%') === false) {
            $value = "%$value%";
        }

        $this->grid->getConfig()->getDataProvider()->filter(
            $this->config->getName(),
            $this->config->getOperator(),
            $value
        );
    }

    /**
     * @param string $value
     *
     * @return mixed Updated value
     */
    private function processValue(string $value)
    {
        if (0 === strpos($value, '=')) {
            $this->config->setOperator(FilterConfig::OPERATOR_EQ);

            return substr($value, 1);
        }

        if (0 === strpos($value, '!')) {
            $this->config->setOperator(FilterConfig::OPERATOR_NOT_EQ);

            return substr($value, 1);
        }

        if (0 === strpos($value, '>')) {
            $this->config->setOperator(FilterConfig::OPERATOR_GT);

            return substr($value, 1);
        }

        if (0 === strpos($value, '>=')) {
            $this->config->setOperator(FilterConfig::OPERATOR_GTE);

            return substr($value, 2);
        }

        if (0 === strpos($value, '<')) {
            $this->config->setOperator(FilterConfig::OPERATOR_LS);

            return substr($value, 1);
        }

        if (0 === strpos($value, '<=')) {
            $this->config->setOperator(FilterConfig::OPERATOR_LSE);

            return substr($value, 2);
        }

        if (false !== strpos($value, ',')) {
            $this->config->setOperator(FilterConfig::OPERATOR_IN);

            return array_filter(explode(',', $value), 'strlen');
        }

        return $value;
    }
}
