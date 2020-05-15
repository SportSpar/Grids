<?php

namespace SportSpar\Grids\Components\Base;

use Illuminate\Support\Collection;
use SportSpar\Grids\Grid;

trait ComponentsContainerTrait
{
    /**
     * @var Collection|ComponentInterface[]
     */
    protected $components;

    /**
     * Returns default child components.
     *
     * Override this method.
     *
     * @return array
     */
    protected function getDefaultComponents(): array
    {
        return [];
    }

    /**
     * Returns child components.
     *
     * @return Collection|ComponentInterface[]
     */
    final public function getComponents(): Collection
    {
        if ($this->components === null) {
            $this->setComponents($this->getDefaultComponents());
        }

        return $this->components;
    }

    /**
     * Finds child component by name.
     *
     * @param string $name
     *
     * @return null|ComponentInterface
     */
    public function getComponentByName($name)
    {
        foreach ($this->getComponents() as $component) {
            if ($component->getName() === $name) {
                return $component;
            }
        }

        return null;
    }

    /**
     * Finds child component by name recursively.
     *
     * @param string $name
     * @return null|ComponentInterface
     */
    public function getComponentByNameRecursive($name)
    {
        foreach ($this->getComponents() as $component) {
            if ($component->getName() === $name) {
                return $component;
            }
            if ($component instanceof ComponentsContainerTrait || $component instanceof ComponentsContainerInterface) {
                if ($res = $component->getComponentByNameRecursive($name)) {
                    return $res;
                }
            }

        }
        return null;
    }

    /**
     * Adds component to the collection of child components.
     *
     * @param ComponentInterface $component
     *
     * @return self
     */
    public function addComponent(ComponentInterface $component)
    {
        $this->getComponents()->push($component);
        $component->setParent($this);

        return $this;
    }

    /**
     * Adds set of components to the collection of child components.
     *
     * @param  Collection|array  $components
     * @return self
     */
    public function addComponents($components)
    {
        foreach ($components as $component) {
            $this->addComponent($component);
        }

        return $this;
    }

    /**
     * Allows to specify collection of child components.
     *
     * @param Collection|ComponentInterface[]|array $components
     *
     * @return $this
     */
    public function setComponents($components)
    {
        $this->components = new Collection($components);
        foreach ($components as $component) {
            $component->setParent($this);
        }

        return $this;
    }

    /**
     * Creates component,
     * adds it to child components collection and returns it.
     *
     * @param string $class
     * @return ComponentInterface
     */
    public function makeComponent($class)
    {
        $component = new $class;
        $this->addComponent($component);

        return $component;
    }

    /**
     * Initializes child components.
     *
     * @param Grid $grid
     */
    public function initializeComponents(Grid $grid)
    {
        foreach ($this->getComponents() as $component) {
            $component->initialize($grid);
        }
    }

    /**
     * Prepares child components for rendering.
     */
    public function prepareComponents()
    {
        foreach ($this->getComponents() as $component) {
            $component->prepare();
        }
    }
}
