<?php

namespace SportSpar\Grids\DataProvider;

use Illuminate\Support\Collection;
use SportSpar\Grids\Exception\DataProvider\UnsupportedDataSource;

class DataProviderFactory
{
    /**
     * @var string[]
     */
    protected $providers = [
        CollectionDataProvider::class,
        EloquentDataProvider::class,
        DbalDataProvider::class
    ];

    /**
     * @param mixed $source
     *
     * @return DataProviderInterface
     *
     * @throws UnsupportedDataSource
     */
    public function provideFor($source)
    {
        // If we have a provider, then we're good to go
        if (is_object($source) && $source instanceof DataProviderInterface) {
            return $source;
        }

        if (is_array($source)) {
            return new CollectionDataProvider(new Collection($source));
        }

        // Model name
        if (is_string($source) && class_exists($source, true) && is_subclass_of($source, '\Illuminate\Database\Eloquent\Model')) {
            return new EloquentDataProvider(new $source());
        }

        if (is_object($source)) {
            /** @var DataProviderInterface $providerClass */
            foreach ($this->providers as $providerClass) {
                if ($providerClass::canProvideFor($source)) {
                    return new $providerClass($source);
                }
            }
        }

        throw new UnsupportedDataSource('Unsupported data source');
    }
}
