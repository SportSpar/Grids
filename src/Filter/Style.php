<?php

namespace SportSpar\Grids\Filter;

class Style
{
    private $cssClasses = ['form-control', 'input-sm'];

    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }

    public function setCssClasses(array $cssClasses)
    {
        $this->cssClasses = $cssClasses;
    }

    public function addCssClass(string $class)
    {
        $this->cssClasses[] = $class;
    }
}