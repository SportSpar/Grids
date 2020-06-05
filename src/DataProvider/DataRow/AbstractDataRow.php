<?php

namespace SportSpar\Grids\DataProvider\DataRow;

abstract class AbstractDataRow implements DataRowInterface
{
    /**
     * Row data
     *
     * @var mixed
     */
    protected $src;

    /**
     * Row number
     *
     * @var int
     */
    protected $id;

    /**
     * @param mixed $src
     * @param int   $id
     */
    public function __construct($src, $id)
    {
        $this->src = $src;
        $this->id = $id;
    }

    /**
     * Returns row id.
     *
     * It's row number starting from 1, considering pagination.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns row data source.
     *
     * @return mixed
     */
    public function getSrc()
    {
        return $this->src;
    }
}
