<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\Components\Base\ComponentInterface;

/**
 * Class TFoot
 *
 * The component for rendering TFOOT html tag inside grid.
 */
class TFoot extends HtmlTag
{
    const NAME = 'tfoot';

    /**
     * Returns default set of child components.
     *
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents(): array
    {
        return [
            (new OneCellRow())->addComponent(new Pager())
        ];
    }
}
