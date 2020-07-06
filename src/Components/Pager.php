<?php

namespace SportSpar\Grids\Components;

use Illuminate\Pagination\Paginator;
use Request;
use SportSpar\Grids\Components\Base\RenderableComponent;
use SportSpar\Grids\Grid;

class Pager extends RenderableComponent
{
    protected $input_key;

    protected $name = 'pager';

    public function render()
    {
        $this->setupPaginationForLinks();

        return (string)$this->links();
    }

    protected function setupPaginationForReading()
    {
        Paginator::currentPageResolver(function () {
            return $this->grid->getInputProcessor()->getValue('page', 1);
        });
    }

    protected function setupPaginationForLinks()
    {
        $this->grid->getConfig()
                   ->getDataProvider()
                   ->getPaginator()
                   ->setPageName("{$this->input_key}[page]");
    }

    /**
     * Renders pagination links & returns rendered html.
     */
    protected function links()
    {
        $paginator = $this->grid->getConfig()
                                ->getDataProvider()
                                ->getPaginator();
        $input = $this->grid->getInputProcessor()->getInput();
        if (isset($input['page'])) {
            unset($input['page']);
        }

        return str_replace('/?', '?', $paginator->appends($this->input_key, $input)->render());
    }

    public function initialize(Grid $grid)
    {
        parent::initialize($grid);
        $this->input_key = $grid->getInputProcessor()->getKey();
        $this->setupPaginationForReading();
    }
}
