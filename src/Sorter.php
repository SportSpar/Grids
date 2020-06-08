<?php

namespace SportSpar\Grids;

use SportSpar\Grids\InputProvider\LaravelRequest;

class Sorter
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Returns URL for sorting control.
     *
     * @param FieldConfig $column
     * @param $direction
     * @return string
     */
    public function link(FieldConfig $column, $direction)
    {
        return (new LaravelRequest($this->grid->getConfig()->getName()))
            ->setSorting($column->getName(), $direction)
            ->getUrl();
    }

    /**
     * @param string $columnName
     * @param string $direction   Possible values: asc, desc
     */
    public function setDefaultSort(string $columnName, string $direction = 'DESC')
    {
        $inputProcessor = $this->grid->getInputProcessor();

        if (empty($inputProcessor->getSorting())) {
            $inputProcessor->setSorting($columnName, strtoupper($direction));
        }
    }

    /**
     * Applies sorting to data provider.
     */
    public function apply()
    {
        $input = $this->grid->getInputProcessor()->getInput();
        $sort = null;
        if (isset($input['sort'])) {
            foreach ($input['sort'] as $field => $direction) {
                $sort = [$field, $direction];
                break;
            }
        }
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            if ($sort) {
                if ($column->getName() === $sort[0]) {
                    $column->setSorting($sort[1]);
                } else {
                    $column->setSorting(null);
                }
            } else {
                if ($direction = $column->getSorting()) {
                    $sort = [$column->getName(), $direction];
                }
            }
        }
        if ($sort) {
            $this
                ->grid
                ->getConfig()
                ->getDataProvider()
                ->orderBy($sort[0], $sort[1]);
        }
    }
}
