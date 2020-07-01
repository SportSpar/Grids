<?php

namespace SportSpar\Grids\Components\Base;

/**
 * Interface RenderableInterface
 *
 * Interface for objects that can be rendered.
 */
interface RenderableInterface
{
    /**
     * Renders object.
     *
     * @return string
     */
    public function render();

    /**
     * Returns template.
     *
     * @return string|null
     */
    public function getTemplate();

    /**
     * Sets template.
     *
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template);

    /**
     * Returns rendering result  when object is treated like a string.
     *
     * @return mixed
     */
    public function __toString();

    /**
     * Returns true if object was rendered.
     *
     * @return bool
     */
    public function isRendered();
}
