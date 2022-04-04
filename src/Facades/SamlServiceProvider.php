<?php

namespace PhilWilliammee\SamlServiceProvider\Facades;

use Illuminate\Support\Facades\Facade;

class SamlServiceProvider extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'samlserviceprovider';
    }
}
