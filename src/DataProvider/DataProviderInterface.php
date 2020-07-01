<?php

namespace SportSpar\Grids\DataProvider;

use Illuminate\Pagination;
use Illuminate\Support;
use SportSpar\Grids\DataProvider\DataRow\DataRowInterface;

interface DataProviderInterface
{
    /**
     * Sets the internal pointer first element.
     *
     * @return self
     */
    public function reset();

    /**
     * Sets page size.
     *
     * @param int $pageSize
     *
     * @return self
     */
    public function setPageSize($pageSize);

    /**
     * Sets current page number. Page numeration starts from 1.
     *
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage);

    /**
     * Returns current page number (starting from 1 by default).
     *
     * @return int
     */
    public function getCurrentPage();

    /**
     * Sets data sorting.
     *
     * @param string $fieldName
     * @param        $direction
     *
     * @return $this
     */
    public function orderBy($fieldName, $direction);

    /**
     * Performs filtering.
     *
     * @param string $fieldName
     * @param string $operator
     * @param mixed  $value
     *
     * @return $this
     */
    public function filter($fieldName, $operator, $value);

    /**
     * Returns collection of raw data items.
     *
     * @return Support\Collection
     */
    public function getCollection();

    /**
     * @return Pagination\Paginator
     */
    public function getPaginator();

    /**
     * Fetches one row and moves internal pointer forward.
     * When last row fetched, returns null
     *
     * @return DataRowInterface|null
     */
    public function getRow();

    /**
     * Returns count of records on current page.
     *
     * @return int
     *
     * @deprecated
     *
     * @todo rename to something like recordsOnPage
     */
    public function count();

    /**
     * Return true if this provider can provider for $className
     *
     * @param object $object
     *
     * @return bool
     */
    public static function canProvideFor($object): bool;
}
