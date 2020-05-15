<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Components\Base\ComponentInterface;

/**
 * Class THead
 *
 * The component for rendering THEAD html tag inside grid.
 *
 * @package SportSpar\Grids\Components
 */
class THead extends HtmlTag
{
    const NAME = 'thead';

    /**
     * Returns default set of child components.
     *
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        return [
            new ColumnHeadersRow(),
            new FiltersRow()
        ];
    }
}
