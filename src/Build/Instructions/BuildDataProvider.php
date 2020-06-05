<?php

namespace SportSpar\Grids\Build\Instructions;

use Nayjest\Builder\Instructions\Base\Instruction;
use Nayjest\Builder\Scaffold;
use SportSpar\Grids\DataProvider\DataProviderFactory;
use SportSpar\Grids\DataProvider\EloquentDataProvider;
use SportSpar\Grids\Exception\DataProvider\UnsupportedDataSource;

/**
 * This class is a build instruction for nayjest/build package
 * that defines how to setup grids data provider
 */
class BuildDataProvider extends Instruction
{
    /**
     * @var int
     */
    protected $phase = self::PHASE_PRE_INST;

    /**
     * @param Scaffold $scaffold
     *
     * @throws UnsupportedDataSource
     */
    public function apply(Scaffold $scaffold)
    {
        $src = $scaffold->getInput('src');
        $scaffold->excludeInput('src');

        $factory  = new DataProviderFactory();
        $provider = $factory->provideFor($src);

        $scaffold->input['data_provider'] = $provider;

        // This does not belong here, but it's still a tad better, than in builder
        if ($provider instanceof EloquentDataProvider && !$scaffold->getInput('columns')) {
            $table = $provider->getBuilder()->getModel()->getTable();
            $columns = DB
                ::connection()
                ->getSchemaBuilder()
                ->getColumnListing($table);
            $scaffold->input['columns'] = $columns;
        }
    }
}

