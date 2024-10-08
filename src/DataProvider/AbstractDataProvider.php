<?php

namespace SportSpar\Grids\DataProvider;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use SportSpar\Grids\DataProvider\DataRow\DataRowInterface;

abstract class AbstractDataProvider implements DataProviderInterface
{
    public const EVENT_FETCH_ROW = 'grid.dp.fetch_row';

    protected $src;

    protected $index = 0;

    protected $page_size = 100;

    protected $current_page = 1;

    /**
     * Constructor.
     *
     * @param mixed $src data source
     */
    public function __construct($src)
    {
        $this->src = $src;
    }

    /**
     * Sets the internal pointer first element.
     *
     * @return self
     */
    public function reset()
    {
        reset($this->src);

        return $this;
    }

    /**
     * Sets page size.
     *
     * @param int $pageSize
     *
     * @return self
     */
    public function setPageSize($pageSize)
    {
        $this->page_size = $pageSize;

        return $this;
    }

    /**
     * Sets current page number. Page numeration starts from 1.
     *
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->current_page = $currentPage;
    }

    /**
     * Returns current page number (starting from 1 by default).
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * @return int row id starting from 1, considering pagination
     */
    protected function getRowId()
    {
        $offset = ($this->getCurrentPage() - 1) * $this->page_size;

        return $offset + $this->index;
    }

    /**
     * Sets data sorting.
     *
     * @param string $fieldName
     * @param        $direction
     *
     * @return self
     */
    abstract public function orderBy($fieldName, $direction);

    /**
     * Performs filtering.
     *
     * @param string $fieldName
     * @param string $operator
     * @param mixed  $value
     *
     * @return self
     */
    abstract public function filter($fieldName, $operator, $value);

    /**
     * Returns collection of raw data items.
     *
     * @return Collection
     */
    abstract public function getCollection();

    /**
     * @return Paginator
     */
    abstract public function getPaginator();

    /**
     * Fetches one row and moves internal pointer forward.
     * When last row fetched, returns null
     *
     * @return DataRowInterface|null
     */
    abstract public function getRow();

    /**
     * Returns count of records on current page.
     *
     * @todo rename to something like recordsOnPage
     *
     * @deprecated
     *
     * @return int
     */
    abstract public function count();
}
