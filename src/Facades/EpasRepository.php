<?php

namespace Jlab\EpasRepository\Facades;

use Illuminate\Support\Facades\Facade;

class EpasRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'epas-repository';
    }
}
