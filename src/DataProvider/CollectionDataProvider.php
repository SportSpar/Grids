<?php

namespace SportSpar\Grids\DataProvider;

use ArrayIterator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use SportSpar\Grids\ArrayDataRow;
use SportSpar\Grids\DataProvider;

class CollectionDataProvider extends DataProvider
{
    /**
     * @var Collection
     */
    protected $src;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var ArrayIterator
     */
    private $iterator;

    /**
     * @param Collection $src
     */
    public function __construct(Collection $src)
    {
        parent::__construct($src);
    }

    /**
     * @inheritDoc
     */
    public function orderBy($fieldName, $direction)
    {
        $descending = (strcasecmp($direction, 'desc') === 0);
        $options = SORT_REGULAR;

        $this->src = $this->src->sortBy($fieldName, $options, $descending);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filter($fieldName, $operator, $value)
    {
        // A do-nothing callback
        $callback = function($item) { return $item; };

        switch ($operator) {
            case "like":
                $callback = function($item) use ($value, $fieldName) {
                    $value = trim($value, '%');

                    if (empty($value)) {
                        return true;
                    }

                    return stripos((string)$item[$fieldName], $value) !== false;
                };
                break;
            case "eq":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] == $value; };
                break;
            case "n_eq":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] != $value; };
                break;
            case "gt":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] > $value; };
                break;
            case "lt":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] < $value; };
                break;
            case "ls_e":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] <= $value; };
                break;
            case "gt_e":
                $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] >= $value; };
                break;
            case "in":
                if (!is_array($value)) {
                    $callback = function($item) use ($value, $fieldName) { return (string)$item[$fieldName] == $value; };
                    break;
                }
                $callback = function($item) use ($value, $fieldName) { return in_array((string)$item[$fieldName], $value); };
                return $this;
        }

        $this->src = $this->src->filter($callback);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return new Collection($this->getPaginator()->items());
    }

    /**
     * @inheritDoc
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            // Pre paginate
            $items = $this->src->forPage($this->getCurrentPage(), $this->page_size);

            $this->paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $this->count(),
                $this->page_size,
                $this->getCurrentPage(),
                [
                    'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()
                ]
            );
        }
        return $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function getPaginationFactory()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRow()
    {
        $iterator = $this->getIterator();

        if (!$iterator->valid()) {
            return null;
        }

        $key     = $iterator->key();
        $current = $iterator->current();

        $iterator->next();

        return new ArrayDataRow($current, $key);
    }

    /**
     * @return ArrayIterator
     */
    protected function getIterator(): ArrayIterator
    {
        if (!$this->iterator) {
            $this->iterator = $this->getCollection()->getIterator();
        }
        return $this->iterator;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->src->count();
    }

    /**
     * @inheritDoc
     */
    public static function canProvideFor($object): bool
    {
        return $object instanceof Collection;
    }
}
