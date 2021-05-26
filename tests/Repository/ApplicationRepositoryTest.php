<?php

namespace Jlab\EpasRepository\Tests;


use Illuminate\Support\Facades\Config;
use Jlab\EpasRepository\Repository\ApplicationRepository;

class ApplicationRepositoryTest extends TestCase{

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('epas-repository.webServices', 'https://epas-rk95-dev.staging.prometheusgroup.app/webservices');
    }


    function test_it_generates_correct_wsdl_url(){
        $repo = new ApplicationRepository();
        $this->assertEquals('https://epas-rk95-dev.staging.prometheusgroup.app/webservices/Integration/ApplicationWebService.asmx?wsdl', $repo->wsdl());
    }



}

