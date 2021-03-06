<?php

namespace SportSpar\Grids\Components\Base;

use SportSpar\Grids\Grid;

/**
 * ComponentInterface interface implementation.
 *
 * @see SportSpar\Grids\Components\Base\ComponentInterface
 */
trait ComponentTrait
{
    /**
     * @var ComponentsContainerInterface
     */
    protected $parent;

    /** @var Grid */
    protected $grid;

    /** @var string|null */
    protected $name;

    /**
     * Attaches component to registry.
     *
     * @param ComponentsContainerInterface $parent
     */
    public function setParent(ComponentsContainerInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns parent object.
     *
     * @return ComponentsContainerInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Initializes component with grid.
     *
     * @param Grid $grid
     *
     * @return null
     */
    public function initialize(Grid $grid)
    {
        $this->grid = $grid;
        if (method_exists($this, 'initializeComponents')) {
            $this->initializeComponents($grid);
        }
    }

    /**
     * Returns component name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets component name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Performs all required operations before rendering component.
     *
     * @return mixed
     */
    public function prepare()
    {
        if (method_exists($this, 'initializeComponents')) {
            $this->prepareComponents();
        }
    }
}
