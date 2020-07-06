<?php

namespace SportSpar\Grids\DataProvider;

use ArrayIterator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use SportSpar\Grids\DataProvider\DataRow\ObjectDataRow;

class EloquentDataProvider extends AbstractDataProvider
{
    protected $collection;

    protected $paginator;

    /**
     * @var Builder
     */
    protected $src;

    /**
     * @var ArrayIterator
     */
    protected $iterator;

    /**
     * Constructor.
     *
     * @param Builder $src
     */
    public function __construct(Builder $src)
    {
        parent::__construct($src);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->getIterator()->rewind();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = Collection::make(
                $this->getPaginator()->items()
            );
        }

        return $this->collection;
    }

    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->src->paginate($this->page_size);
        }

        return $this->paginator;
    }

    protected function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = $this->getCollection()->getIterator();
        }

        return $this->iterator;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->src;
    }

    public function getRow()
    {
        if ($this->index < $this->count()) {
            $this->index++;
            $item = $this->iterator->current();
            $this->iterator->next();
            $row = new ObjectDataRow($item, $this->getRowId());

            Event::dispatch(self::EVENT_FETCH_ROW, $this);

            return $row;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getCollection()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($fieldName, $direction)
    {
        $this->src->orderBy($fieldName, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($fieldName, $operator, $value)
    {
        $this->filterWhere($fieldName, $operator, $value);
    }

    /**
     * @param string $operator
     *
     * @return string
     */
    private function translateOperator($operator): string
    {
        $operators = [
            'eq'   => '=',
            'n_eq' => '<>',
            'gt'   => '>',
            'lt'   => '<',
            'ls_e' => '<=',
            'gt_e' => '>=',
        ];

        return $operators[$operator] ?? $operator;
    }

    /**
     * @param string       $fieldName
     * @param string       $operator
     * @param array|string $value
     *
     * @return static
     */
    public function filterWhere($fieldName, $operator, $value)
    {
        $operator = strtolower($this->translateOperator($operator));

        if ($operator === 'like' && strpos($value, '%') === false) {
            $value = "%$value%";
        }

        if ($operator === 'in') {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->src->whereIn($fieldName, $value);

            return $this;
        }

        $this->src->where($fieldName, $operator, $value);

        return $this;
    }

    /**
     * @param string       $fieldName
     * @param string       $operator
     * @param array|string $value
     *
     * @return static
     */
    public function filterHaving($fieldName, $operator, $value)
    {
        $operator = $this->translateOperator($operator);

        if ($operator === 'like' && strpos($value, '%') === false) {
            $value = "%$value%";
        }

        if ($operator === 'in') {
            if (!is_array($value)) {
                $value = [$value];
            }

            $placeholders = $this->generatePlaceholders($value);
            $this->src->havingRaw("$fieldName IN ($placeholders)", $value);

            return $this;
        }

        $this->src->havingRaw("$fieldName $operator ?", [$value]);

        return $this;
    }

    /**
     * @param $values
     *
     * @return string
     */
    private function generatePlaceholders($values): string
    {
        if (!is_array($values)) {
            return '?';
        }

        return implode(',', array_fill(0, count($values), '?'));
    }

    /**
     * {@inheritdoc}
     */
    public static function canProvideFor($object): bool
    {
        return $object instanceof Builder;
    }
}
