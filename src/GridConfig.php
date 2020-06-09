<?php

namespace SportSpar\Grids;

use Illuminate\Support\Collection;
use SportSpar\Grids\Components\Base\RenderableComponentInterface;
use SportSpar\Grids\Components\Base\ComponentTrait;
use SportSpar\Grids\Components\Base\ComponentsContainerTrait;
use SportSpar\Grids\Components\Base\ComponentsContainerInterface;
use SportSpar\Grids\Components\TFoot;
use SportSpar\Grids\Components\THead;
use SportSpar\Grids\Components\Tr;
use SportSpar\Grids\DataProvider\DataProviderInterface;
use SportSpar\Grids\InputProvider\IlluminateRequest;
use SportSpar\Grids\InputProvider\InputProviderInterface;

class GridConfig implements ComponentsContainerInterface
{
    use ComponentsContainerTrait;
    use ComponentTrait;

    const SECTION_DO_NOT_RENDER = 'not_render';

    protected $template = 'grids::default';

    /** @var FieldConfig[]|Collection */
    protected $columns;

    /** @var  DataProviderInterface $data_provider */
    protected $data_provider;

    /**
     * @var InputProviderInterface
     */
    private $inputProvider;

    protected $page_size = 50;

    /** @var Collection|FilterConfig[] $filters */
    protected $filters;

    /** @var int */
    protected $caching_time = 0;

    protected $main_template = '*.grid';

    protected $row_component;

    /**
     * @return RenderableComponentInterface
     */
    public function getRowComponent()
    {
        if (!$this->row_component) {
            $this->row_component = (new Tr)
                ->setRenderSection(self::SECTION_DO_NOT_RENDER);
            if ($this->grid) {
                $this->row_component->initialize($this->grid);
            }
            $this->addComponent($this->row_component);
        }
        return $this->row_component;
    }

    /**
     * @param RenderableComponentInterface $rowComponent
     * @return $this
     */
    public function setRowComponent(RenderableComponentInterface $rowComponent)
    {
        $this->row_component = $rowComponent;
        $this->addComponent($rowComponent);
        $rowComponent->setRenderSection(self::SECTION_DO_NOT_RENDER);
        return $this;
    }

    /**
     * Returns default child components.
     *
     * @return array
     */
    protected function getDefaultComponents(): array
    {
        return [
            new THead(),
            new TFoot()
        ];
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function setMainTemplate($template)
    {
        $this->main_template = $template;
        return $this;
    }

    public function getMainTemplate()
    {
        return str_replace('*.', "$this->template.", $this->main_template);
    }


    /**
     * @param Collection|FilterConfig[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        $this->filters = Collection::make($filters);
        return $this;
    }

    public function getFilters()
    {
        if (null === $this->filters) {
            $this->filters = new Collection();
        }
        return $this->filters;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return $this
     */
    public function setDataProvider(DataProviderInterface $dataProvider)
    {
        $this->data_provider = $dataProvider;
        return $this;
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->data_provider;
    }

    /**
     * @return InputProviderInterface
     */
    public function getInputProvider(): InputProviderInterface
    {
        if (null === $this->inputProvider) {
            $this->inputProvider = new IlluminateRequest($this->getName());
        }

        return $this->inputProvider;
    }

    /**
     * @param InputProviderInterface $inputProvider
     */
    public function setInputProvider(InputProviderInterface $inputProvider)
    {
        $this->inputProvider = $inputProvider;
    }

    /**
     * @param FieldConfig[]|Collection $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = Collection::make($columns);
        return $this;
    }

    /**
     * Returns collection of grid columns.
     *
     * @return FieldConfig[]|Collection
     */
    public function getColumns()
    {
        if (null === $this->columns) {
            $this->columns = new Collection;
        }
        return $this->columns;
    }

    /**
     * Returns column by name.
     *
     * @param string $name
     * @return null|FieldConfig
     */
    public function getColumn($name)
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

    }

    /**
     * Returns cache expiration time in minutes.
     *
     * @return int
     */
    public function getCachingTime()
    {
        return $this->caching_time;
    }

    /**
     * Sets cache expiration time in minutes.
     *
     * @param int $minutes
     *
     * @return $this
     */
    public function setCachingTime($minutes)
    {
        $this->caching_time = $minutes;
        return $this;
    }

    /**
     * Adds column to grid.
     *
     * @param FieldConfig $column
     * @return $this
     */
    public function addColumn(FieldConfig $column)
    {
        if ($this->columns === null) {
            $this->setColumns([]);
        }
        $this->columns->push($column);
        return $this;
    }

    /**
     * Sets maximal quantity of rows per page.
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->page_size = (int)$pageSize;
        return $this;
    }

    /**
     * Returns maximal quantity of rows per page.
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->page_size;
    }
}
