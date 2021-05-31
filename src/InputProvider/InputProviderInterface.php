<?php

namespace SportSpar\Grids\InputProvider;

interface InputProviderInterface
{
    /**
     * @return array
     */
    public function getInput();

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return mixed
     */
    public function getSorting();

    /**
     * @param string $column
     * @param string $direction
     */
    public function setSorting($column, $direction);

    /**
     * @param string $filterName
     * @param string $value
     */
    public function setFilterValue($filterName, $value);

    /**
     * @param string $filterName
     *
     * @return mixed
     */
    public function getFilterValue($filterName);

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null);

    /**
     * @return string
     */
    public function getUniqueRequestId();

    /**
     * @return string
     */
    public function getSortingHiddenInputsHtml(): string;

    /**
     * @param array $newParams
     *
     * @return string
     */
    public function getUrl(array $newParams = []): string;
}
