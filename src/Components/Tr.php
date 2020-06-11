<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\DataProvider\DataRow\DataRowInterface;

/**
 * Class Tr
 *
 * The component for rendering TR html tag inside grid.
 */
class Tr extends HtmlTag
{
    /**
     * @var DataRowInterface
     */
    protected $dataRow;

    /**
     * Returns data row.
     *
     * @return DataRowInterface
     */
    public function getDataRow()
    {
        return $this->dataRow;
    }

    /**
     * Allows to set data row.
     *
     * @param DataRowInterface $dataRow
     *
     * @return $this
     */
    public function setDataRow(DataRowInterface $dataRow)
    {
        $this->dataRow = $dataRow;

        return $this;
    }

    /**
     * Renders row cells.
     *
     * @return string
     */
    protected function renderCells()
    {
        $row = $this->getDataRow();
        $out = '';
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            $component = new TableCell($column);
            $component->initialize($this->grid);
            $component->setContent($column->getValue($row));
            $out .= $component->render();
        }

        return $out;
    }

    /**
     * Returns tag content.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->getDataRow() ? $this->renderCells() : parent::getContent();
    }
}
