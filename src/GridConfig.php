<?php

namespace SportSpar\Grids;

use Illuminate\Support\Collection;
use SportSpar\Grids\Components\Base\ComponentInterface;
use SportSpar\Grids\Components\Base\ComponentsContainerInterface;
use SportSpar\Grids\Components\Base\ComponentsContainerTrait;
use SportSpar\Grids\Components\Base\ComponentTrait;
use SportSpar\Grids\Components\Base\RenderableComponentInterface;
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

    public const SECTION_DO_NOT_RENDER = 'not_render';

    protected $template = 'grids::default';

    /** @var FieldConfig[]|Collection */
    protected $columns;

    /**
     * @var DataProviderInterface
     */
    protected $data_provider;

    /**
     * @var InputProviderInterface
     */
    private $inputProvider;

    protected $page_size = 50;

    /**
     * @var Collection|FilterConfig[]
     */
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
            $this->row_component = (new Tr())
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
     *
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
     *
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
     *
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
     *
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
     *
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
            $this->columns = new Collection();
        }

        return $this->columns;
    }

    /**
     * Returns column by name.
     *
     * @param string $name
     *
     * @return FieldConfig|null
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
     *
     * @return $this
     */
    public function addColumn(FieldConfig $column): GridConfig
    {
        if ($this->columns === null) {
            $this->setColumns([]);
        }
        $this->columns->push($column);

        return $this;
    }

    /**
     * @param string $columnName
     * @param string $label
     *
     * @return FieldConfig
     */
    public function createColumn(string $columnName, string $label): FieldConfig
    {
        $fieldConfig = (new FieldConfig($columnName))
            ->setLabel($label)
            ->setSortable(true);

        $this->addColumn($fieldConfig);

        return $fieldConfig;
    }

    /**
     * When you modify columns after builder, you need to re-run initialization
     *
     * @param Grid $grid
     */
    public function reinitializeHeader(Grid $grid)
    {
        $header = $this->header();
        if ($header) {
            $header->getComponents()[0]->setComponents([]);
            $header->initialize($grid);
        }
    }

    /**
     * Sets maximal quantity of rows per page.
     *
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize(int $pageSize)
    {
        $this->page_size = $pageSize;

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

    /**
     * Returns footer component.
     *
     * @return TFoot|ComponentInterface|null
     */
    public function footer()
    {
        return $this->getComponentByName(TFoot::NAME);
    }

    /**
     * Returns header component.
     *
     * @return THead|ComponentInterface|null
     */
    public function header()
    {
        return $this->getComponentByName(THead::NAME);
    }
}
