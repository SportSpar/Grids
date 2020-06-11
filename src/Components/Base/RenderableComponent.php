<?php

namespace SportSpar\Grids\Components\Base;

/**
 * Class RenderableComponent
 *
 * Base class for components that can be rendered.
 */
class RenderableComponent implements RenderableComponentInterface
{
    use ComponentTrait;
    use TComponentView;
}
