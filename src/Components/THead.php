<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Components\Base\ComponentInterface;

/**
 * Class THead
 *
 * The component for rendering THEAD html tag inside grid.
 */
class THead extends HtmlTag
{
    public const NAME = 'thead';

    /**
     * Returns default set of child components.
     *
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents(): array
    {
        return [
            new ColumnHeadersRow(),
            new FiltersRow()
        ];
    }
}
