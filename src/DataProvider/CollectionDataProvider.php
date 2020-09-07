<?php

namespace SportSpar\Grids\DataProvider;

use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use SportSpar\Grids\DataProvider\DataRow\ArrayDataRow;

class CollectionDataProvider extends AbstractDataProvider
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
     * @param Collection|array|Arrayable $src
     */
    public function __construct($src)
    {
        parent::__construct(new Collection($src));
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($fieldName, $direction)
    {
        $descending = (strcasecmp($direction, 'desc') === 0);
        $options = SORT_REGULAR;

        $this->src = $this->src->sortBy($fieldName, $options, $descending);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($fieldName, $operator, $value)
    {
        // A do-nothing callback
        $callback = function ($item) { return $item; };

        switch ($operator) {
            case 'like':
                $callback = function ($item) use ($value, $fieldName) {
                    $value = trim($value, '%');

                    if (empty($value)) {
                        return true;
                    }

                    return stripos((string)$item[$fieldName], $value) !== false;
                };
                break;
            case 'eq':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] == $value; };
                break;
            case 'n_eq':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] != $value; };
                break;
            case 'gt':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] > $value; };
                break;
            case 'lt':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] < $value; };
                break;
            case 'ls_e':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] <= $value; };
                break;
            case 'gt_e':
                $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] >= $value; };
                break;
            case 'in':
                if (!is_array($value)) {
                    $callback = function ($item) use ($value, $fieldName) { return (string)$item[$fieldName] == $value; };
                    break;
                }
                $callback = function ($item) use ($value, $fieldName) { return in_array((string)$item[$fieldName], $value); };

                return $this;
        }

        $this->src = $this->src->filter($callback);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return new Collection($this->getPaginator()->items());
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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

        $row = new ArrayDataRow($current, $key);

        Event::dispatch(self::EVENT_FETCH_ROW, [$row, $this]);

        return $row;
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
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->src->count();
    }

    /**
     * {@inheritdoc}
     */
    public static function canProvideFor($object): bool
    {
        return $object instanceof Collection;
    }
}
