<?php

namespace SportSpar\Grids\Components\Base;

use Illuminate\Support\Collection;

/**
 * Interface of Grid components registry
 *
 * @package SportSpar\Grids\Components\Base
 */
interface ComponentsContainerInterface
{
    /**
     * Returns collection of attached components.
     *
     * @return Collection|ComponentInterface[]
     */
    public function getComponents(): Collection;

    /**
     * Returns child component
     * with specified name or null if component not found.
     *
     * @param string $name
     * @return ComponentInterface|null
     */
    public function getComponentByName($name);

    /**
     * Adds component to collection.
     *
     * @param ComponentInterface $component
     * @return self
     */
    public function addComponent(ComponentInterface $component);

    /**
     * Sets children components collection.
     *
     * @param Collection|ComponentInterface[]|array $components
     *
     * @return self
     */
    public function setComponents($components);

    /**
     * Adds components to collection.
     *
     * @param Collection|ComponentInterface[]|array $components
     *
     * @return self
     */
    public function addComponents($components);

    /**
     * Creates component be class name,
     * attaches it to children collection
     * and returns this component as result.
     *
     * @param string $class
     * @return ComponentInterface
     */
    public function makeComponent($class);
}
