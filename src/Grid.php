<?php

namespace SportSpar\Grids;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use SportSpar\Grids\Components\Base\ComponentInterface;
use SportSpar\Grids\Components\TFoot;
use SportSpar\Grids\Components\THead;
use SportSpar\Grids\InputProvider\InputProviderInterface;

class Grid
{
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    public const EVENT_PREPARE = 'grid.prepare';
    public const EVENT_CREATE = 'grid.create';

    /** @var GridConfig */
    protected $config;

    /** @var bool */
    protected $prepared = false;

    /** @var Sorter */
    protected $sorter;

    protected $filtering;

    public function __construct(GridConfig $config)
    {
        $this->config = $config;
        if ($config->getName() === null) {
            $this->provideName();
        }

        $this->initializeComponents();

        Event::dispatch(self::EVENT_CREATE, $this);
    }

    /**
     * @return string
     */
    protected function getMainTemplate()
    {
        return $this->config->getMainTemplate();
    }

    public function prepare()
    {
        if ($this->prepared === true) {
            return;
        }
        $cfg = $this->config;
        $cfg->getDataProvider()
            ->setPageSize(
                $cfg->getPageSize()
            )
            ->setCurrentPage(
                $this->getInputProcessor()->getValue('page', 1)
            );
        $this->getConfig()->prepare();
        $this->getFiltering()->apply();
        $this->prepareColumns();
        $this->getSorter()->apply();

        Event::dispatch(self::EVENT_PREPARE, $this);

        $this->prepared = true;
    }

    protected function initializeComponents()
    {
        $this->getConfig()->initialize($this);
    }

    protected function prepareColumns()
    {
        if ($this->needToSortColumns()) {
            $this->sortColumns();
        }
    }

    /**
     * Provides unique name for each grid on the page
     */
    protected function provideName()
    {
        $backtraceLimit = 10;

        $backtrace = debug_backtrace(0, $backtraceLimit);

        // Find everything before vendor, this is where your project is installed
        // It may so happen, that it is deployed on different servers and has different paths
        // So we need to drop that prefix, to make it independent from the server path
        $prefix = strstr($backtrace[0]['file'], '/vendor', true);

        $str = '';

        // Skip first two entries, these are Grids entries and will be same every time
        for ($id = 2; $id < $backtraceLimit; $id++) {
            $trace = $backtrace[$id] ?? [];
            if (empty($trace['class']) || !$this instanceof $trace['class']) {
                // may be closure
                if (isset($trace['file'], $trace['line'])) {
                    $str .= str_replace($prefix, '', $trace['file']) . $trace['line'];
                }
            }
        }

        $this->config->setName(substr(md5($str), 0, 16));
    }

    /**
     * Returns true if columns must be sorted.
     *
     * @return bool
     */
    protected function needToSortColumns()
    {
        foreach ($this->config->getColumns() as $column) {
            if ($column->getOrder() !== 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sorts columns according to its order.
     */
    protected function sortColumns()
    {
        $this->config->getColumns()->sort(function (FieldConfig $a, FieldConfig $b) {
            return $a->getOrder() > $b->getOrder();
        });
    }

    /**
     * Returns data sorting manager.
     *
     * @return Sorter
     */
    public function getSorter()
    {
        if (null === $this->sorter) {
            $this->sorter = new Sorter($this);
        }

        return $this->sorter;
    }

    /**
     * Returns input provider
     *
     * @return InputProviderInterface
     */
    public function getInputProcessor(): InputProviderInterface
    {
        return $this->config->getInputProvider();
    }

    /**
     * @return GridConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getViewData()
    {
        return [
            'grid' => $this,
            'data' => $this->config->getDataProvider(),
            'template' => $this->config->getTemplate(),
            'columns' => $this->config->getColumns()
        ];
    }

    /**
     * Renders grid.
     *
     * @return View|string
     */
    public function render()
    {
        $key = $this->getInputProcessor()->getUniqueRequestId();
        $caching_time = $this->config->getCachingTime();
        if ($caching_time && ($output = Cache::get($key))) {
            return $output;
        }
        $this->prepare();
        $provider = $this->config->getDataProvider();
        $provider->reset();
        $output = View::make(
            $this->getMainTemplate(),
            $this->getViewData()
        )->render();
        if ($caching_time) {
            Cache::put($key, $output, $caching_time);
        }

        return $output;
    }

    /**
     * Returns footer component.
     *
     * @return TFoot|ComponentInterface|null
     */
    public function footer()
    {
        return $this->getConfig()->footer();
    }

    /**
     * Returns header component.
     *
     * @return THead|ComponentInterface|null
     */
    public function header()
    {
        return $this->getConfig()->header();
    }

    /**
     * Returns data filtering manager.
     *
     * @return Filtering
     */
    public function getFiltering()
    {
        if ($this->filtering === null) {
            $this->filtering = new Filtering($this);
        }

        return $this->filtering;
    }

    /**
     * Renders grid object when it is treated like a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }
}
