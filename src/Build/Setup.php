<?php

namespace SportSpar\Grids\Build;

use Closure;
use LogicException;
use Nayjest\Builder\Blueprint;
use Nayjest\Builder\BlueprintsCollection;
use Nayjest\Builder\Builder;
use Nayjest\Builder\Env;
use Nayjest\Builder\Instructions\Base\Instruction;
use Nayjest\Builder\Instructions\CustomInstruction;
use Nayjest\Builder\Instructions\Mapping\Build;
use Nayjest\Builder\Instructions\Mapping\BuildChildren;
use Nayjest\Builder\Instructions\Mapping\CallMethodWith;
use Nayjest\Builder\Instructions\Mapping\CustomMapping;
use Nayjest\Builder\Instructions\Mapping\Rename;
use Nayjest\Builder\Instructions\SimpleValueAsField;
use Nayjest\Builder\Scaffold;
use SportSpar\Grids\Build\Instructions\BuildDataProvider;

/**
 * Class Setup
 *
 * This class prepares environment for nayjest/builder package for usage with grids.
 * Integration with nayjest/builder package allows to construct grids from configuration in form of php array.
 *
 * @See \Grids::make
 *
 * @internal
 */
class Setup
{
    public const COLUMN_CLASS = 'SportSpar\Grids\FieldConfig';
    public const COMPONENT_CLASS = 'SportSpar\Grids\Components\Base\ComponentInterface';
    public const GRID_CLASS = 'SportSpar\Grids\GridConfig';
    public const FILTER_CLASS = 'SportSpar\Grids\FilterConfig';

    /**
     * @var BlueprintsCollection
     */
    protected $blueprints;

    /**
     * Creates blueprints required to construct grids from configuration.
     *
     * @return Builder
     */
    public function run()
    {
        $this->blueprints = Env::instance()->blueprints();
        $this->blueprints
            ->add($this->makeFilterBlueprint())
            ->add($this->makeFieldBlueprint())
            ->add($this->makeComponentBlueprint())
            ->add($configBlueprint = $this->makeConfigBlueprint());

        return new Builder($configBlueprint);
    }

    /**
     * Creates main blueprint of grid configuration.
     *
     * @return Blueprint
     */
    protected function makeConfigBlueprint(): Blueprint
    {
        $componentBlueprint = $this->blueprints->getFor(self::COMPONENT_CLASS);
        if (!$componentBlueprint) {
            throw new LogicException('Blueprint for grid components must be created before main blueprint.');
        }

        $column_blueprint = $this->blueprints->getFor(self::COLUMN_CLASS);
        if (!$column_blueprint) {
            throw new LogicException('Blueprint for grid columns must be created before main blueprint.');
        }

        return new Blueprint(self::GRID_CLASS, [
            new BuildDataProvider(),
            new BuildChildren('components', $componentBlueprint),
            new Build('row_component', $componentBlueprint),
            new BuildChildren('columns', $column_blueprint),
        ]);
    }

    /**
     * Creates blueprint for grid components.
     *
     * @return Blueprint
     */
    protected function makeComponentBlueprint()
    {
        $blueprint = new Blueprint(self::COMPONENT_CLASS, [
            new CustomInstruction(function (Scaffold $s) {
                if ($s->input instanceof Closure) {
                    $s->class = 'SportSpar\Grids\Components\RenderFunc';
                    $s->constructor_arguments = [$s->input];
                    $s->input = [];
                } elseif (is_string($s->input)) {
                    $s->class = 'SportSpar\Grids\Components\RenderFunc';
                    $out = $s->input;
                    $s->constructor_arguments = [function () use ($out) {
                        return $out;
                    }];
                    $s->input = [];
                }
            }, Instruction::PHASE_PRE_INST),
            new CustomMapping('type', function ($type, Scaffold $s) {
                if (strpos($type, '\\') !== false) {
                    $s->class = $type;
                } else {
                    $s->class = 'SportSpar\Grids\Components\\' . str_replace(
                        ' ',
                        '',
                        ucwords(str_replace(['-', '_'], ' ', $type))
                    );
                }
            }, null, Instruction::PHASE_PRE_INST)
        ]);
        $blueprint->add(new BuildChildren('components', $blueprint));

        $blueprint->add(new Rename('component', 'add_component'));
        $blueprint->add(new Build('add_component', $blueprint));
        $blueprint->add(new CallMethodWith('add_component', 'addComponent'));

        return $blueprint;
    }

    /**
     * Creates blueprint for filters.
     *
     * @return Blueprint
     */
    protected function makeFilterBlueprint()
    {
        return new Blueprint(self::FILTER_CLASS, [
            new SimpleValueAsField('name'),
            new CustomMapping('type', function ($type, Scaffold $s) {
                switch ($type) {
                    case 'select':
                        $s->class = 'SportSpar\Grids\SelectFilterConfig';
                        break;
                    default:
                        break;
                }
            }, null, Instruction::PHASE_PRE_INST),
            new Rename(0, 'name'),
            new Rename(1, 'operator'),
        ]);
    }

    /**
     * Creates blueprint for grid columns.
     *
     * @return Blueprint
     */
    protected function makeFieldBlueprint()
    {
        $filter_blueprint = $this->blueprints->getFor(self::FILTER_CLASS);
        if (!$filter_blueprint) {
            throw new LogicException('Blueprint for grid filters must be created before grid columns blueprint.');
        }

        return new Blueprint(self::COLUMN_CLASS, [
            new SimpleValueAsField('name'),
            new Rename(0, 'name'),
            new BuildChildren('filters', $filter_blueprint),

            new Rename('filter', 'add_filter'),
            new Build('add_filter', $filter_blueprint),
            new CallMethodWith('add_filter', 'addFilter'),
        ]);
    }
}
