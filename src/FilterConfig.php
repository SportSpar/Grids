<?php

namespace SportSpar\Grids;

class FilterConfig
{
    const OPERATOR_LIKE   = 'like';
    const OPERATOR_EQ     = 'eq';
    const OPERATOR_NOT_EQ = 'n_eq';
    const OPERATOR_GT     = 'gt';
    const OPERATOR_LS     = 'lt';
    const OPERATOR_LSE    = 'ls_e';
    const OPERATOR_GTE    = 'gt_e';
    const OPERATOR_IN     = 'in';

    /**
     * @var FieldConfig
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator = FilterConfig::OPERATOR_EQ;

    /**
     * @var string
     */
    protected $template = '*.input';

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var callable
     */
    private $filteringFunc;

    /**
     * Defines whether operators (<, >, !, ...) in the filter value will be processed
     *
     * @var bool
     */
    private $useRawValue = false;

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     *
     * @return static
     */
    public function setOperator(string $operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return FieldConfig
     */
    public function getColumn(): FieldConfig
    {
        return $this->column;
    }

    /**
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return static
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return callable|null
     */
    public function getFilteringFunc()
    {
        return $this->filteringFunc;
    }

    /**
     * @param callable|null $func ($name, $operator, $value, $dataProvider)
     *
     * @return static
     */
    public function setFilteringFunc($func)
    {
        $this->filteringFunc = $func;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return static
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Returns default filter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Sets default filter value.
     *
     * @param $value
     *
     * @return static
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (null === $this->name && $this->column) {
            $this->name = $this->column->getName();
        }

        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param FieldConfig $column
     */
    public function attach(FieldConfig $column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getName();
    }

    /**
     * @param bool $useRawValue
     */
    public function setUseRawValue(bool $useRawValue)
    {
        $this->useRawValue = $useRawValue;
    }

    /**
     * @return bool
     */
    public function useRawValue(): bool
    {
        return $this->useRawValue;
    }
}
