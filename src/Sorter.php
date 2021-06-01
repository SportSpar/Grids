<?php

namespace SportSpar\Grids;

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

    private function getInputProvider(): InputProvider\InputProviderInterface
    {
        return $this->grid->getInputProcessor();
    }

    /**
     * Returns URL for sorting control.
     *
     * @param FieldConfig $column
     * @param string      $direction
     *
     * @return string
     */
    public function link(FieldConfig $column, string $direction): string
    {
        $inputProvider = clone $this->getInputProvider();
        $inputProvider->setSorting($column->getName(), $direction);

        return $inputProvider->getUrl();
    }

    /**
     * @param string $columnName
     * @param string $direction  Possible values: asc, desc
     */
    public function setDefaultSort(string $columnName, string $direction = 'DESC')
    {
        $inputProvider = $this->getInputProvider();

        if (empty($inputProvider->getSorting())) {
            $inputProvider->setSorting($columnName, strtoupper($direction));
        }
    }

    /**
     * Applies sorting to data provider.
     */
    public function apply()
    {
        $config = $this->grid->getConfig();

        $input = $this->getInputProvider()->getInput();
        $sort = null;

        // Sort just by one column
        if (isset($input['sort'])) {
            foreach ($input['sort'] as $field => $direction) {
                $sort = [$field, $direction];
                break;
            }
        }

        foreach ($config->getColumns() as $column) {
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
            $config->getDataProvider()->orderBy($sort[0], $sort[1]);
        }
    }
}
