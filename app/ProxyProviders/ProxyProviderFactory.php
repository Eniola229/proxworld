<?php

namespace App\ProxyProviders;

use App\Models\Provider;
use App\ProxyProviders\Contracts\ProxyProviderContract;
use InvalidArgumentException;

/**
 * Resolves the right Driver class for a Provider row at runtime. To add a
 * new proxy supplier: write one Driver class implementing
 * ProxyProviderContract, add one row to `providers` with `driver` pointing
 * at its FQCN. Nothing here needs to change.
 */
class ProxyProviderFactory
{
    public static function make(Provider $provider): ProxyProviderContract
    {
        $class = $provider->driver;

        if (! class_exists($class)) {
            throw new InvalidArgumentException("Driver class [{$class}] does not exist for provider [{$provider->name}].");
        }

        $driver = new $class($provider);

        if (! $driver instanceof ProxyProviderContract) {
            throw new InvalidArgumentException("Driver class [{$class}] must implement ProxyProviderContract.");
        }

        return $driver;
    }

    /**
     * Active providers ordered by priority (lowest first = tried first),
     * for the "try the next one on failure" fallback pattern.
     *
     * @return \Illuminate\Support\Collection<int, Provider>
     */
    public static function activeProvidersInPriorityOrder()
    {
        return Provider::active()->get();
    }
}
