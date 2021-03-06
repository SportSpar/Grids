<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Grid;

/**
 * Class ColumnHeadersRow
 *
 * The component for rendering table row with column headers.
 */
class ColumnHeadersRow extends HtmlTag
{
    protected $tag_name = 'tr';

    /**
     * Initializes component with grid
     *
     * @param Grid $grid
     */
    public function initialize(Grid $grid)
    {
        $this->createHeaders($grid);
        parent::initialize($grid);
    }

    /**
     * Creates children components for rendering column headers.
     *
     * @param Grid $grid
     */
    protected function createHeaders(Grid $grid)
    {
        // Remove all previously existing components
        $this->setComponents([]);

        foreach ($grid->getConfig()->getColumns() as $column) {
            $this->addComponent(new ColumnHeader($column));
        }
    }
}
