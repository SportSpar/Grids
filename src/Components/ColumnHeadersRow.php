<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Components\Base\ComponentInterface;
use SportSpar\Grids\Grid;

/**
 * Class ColumnHeadersRow
 *
 * The component for rendering table row with column headers.
 *
 * @package SportSpar\Grids\Components
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
     * @param ComponentInterface $component
     *
     * @return ColumnHeadersRow|void
     */
    public function addComponent(ComponentInterface $component)
    {
        parent::addComponent($component);

        if ($this->grid) {
            $this->initialize($this->grid);
        }

        return $this;
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
