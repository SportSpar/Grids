<?php

namespace SportSpar\Grids\Components;

use Illuminate\Foundation\Application;
use SportSpar\Grids\Components\Base\ComponentInterface;

/**
 * Class TFoot
 *
 * The component for rendering TFOOT html tag inside grid.
 *
 * @package SportSpar\Grids\Components
 */
class TFoot extends HtmlTag
{
    const NAME = 'tfoot';

    /**
     * Returns default set of child components.
     *
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        if (version_compare(Application::VERSION, '5', '<')) {
            $pagerClass = 'SportSpar\Grids\Components\Pager';
        } else {
            $pagerClass = 'SportSpar\Grids\Components\Laravel5\Pager';
        }
        return [
            (new OneCellRow)
                ->addComponent(new $pagerClass)
        ];
    }
}
