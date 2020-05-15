<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Components\Base\RenderableComponent;

/**
 * Class ShowingRecords
 *
 * Renders text: Showing records $from — $to of $total
 *
 * @package SportSpar\Grids\Components
 */
class ShowingRecords extends RenderableComponent
{
    /**
     * @var string
     */
    protected $template = '*.components.showing_records';

    /**
     * Passing $from, $to, $total to view
     * @return mixed
     */
    protected function getViewData()
    {
        $from  = 0;
        $to    = 0;
        $total = 0;

        if ($this->grid) {
            $paginator = $this
                ->grid
                ->getConfig()
                ->getDataProvider()
                ->getPaginator();
            # Laravel 4
            if (method_exists($paginator, 'getFrom')) {
                $from  = $paginator->getFrom();
                $to    = $paginator->getTo();
                $total = $paginator->getTotal();
                # Laravel 5
            } else {
                $from  = $paginator->firstItem();
                $to    = $paginator->lastItem();
                $total = $paginator->total();
            }
        }

        return parent::getViewData() + compact('from', 'to', 'total');
    }
}
