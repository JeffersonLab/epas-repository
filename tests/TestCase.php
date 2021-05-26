<?php


namespace Jlab\EpasRepository\Tests;


use Inertia\ServiceProvider;
use Jlab\Epas\EpasServiceProvider;
use Jlab\LaravelUtilities\PackageServiceProvider;
use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;
use RicorocksDigitalAgency\Soap\Soap;

class TestCase extends \Orchestra\Testbench\TestCase {

    protected function getPackageProviders($app)
    {
        return
            [
                SoapServiceProvider::class
            ];
    }
}
