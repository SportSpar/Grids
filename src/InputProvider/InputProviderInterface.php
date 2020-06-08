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
    public function getKey();

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
}