<?php

namespace Jlab\EpasRepository\Tests;


use Illuminate\Support\Facades\Config;
use Jlab\EpasRepository\Model\Application;
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

    /**
     * NOTE: This is an integration test that will hit the ePAS development server.
     * In order for it to function, the environment variable EPAS_API_AUTH_KEY must be set.
     *
     * ex:  export EPAS_API_AUTH_KEY=########-####-####-####-############
     *
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    function test_it_creates_a_remote_permit_application(){
        if (! env('EPAS_API_AUTH_KEY')){
            $this->addWarning('Integration test skipped because no EPAS_API_AUTH_KEY env was set');
            return false;
        }
        $this->assertNotNull(env('EPAS_API_AUTH_KEY'),'EPAS_API_AUTH_KEY not found in environment variable');
        config([
            'epas-repository' => [
                'applicationWsdl' => __DIR__.DIRECTORY_SEPARATOR.'ApplicationWebServiceWsdl.xml',
                'userName' => 'INTEGRATOR',                     // required for API calls
                'authKey' => env('EPAS_API_AUTH_KEY'),      // required for API calls
                'applicationRules' => [
                    'RemoteRef' => 'required',
                    'Title' => 'required',
                    'ResponsibilityGroupName' => 'required',
                    'ApplicationTypeName' => 'required',
                    'WorkOrderNumber' => 'required',
                ],
            ],
        ]);

        $data = new Application([
            'RemoteRef' => 'ATLIS-20201-'.date('YmdHis'),
            'Title' => 'The Permit Application Title',
            'ResponsibilityGroupName' => 'Accelerator',
            'ApplicationTypeName' => 'Permit Request',
            'WorkOrderNumber' => 'ATLIS-20201',
        ]);
        $repo = new ApplicationRepository();
        $application = $repo->save($data);
        $this->assertEquals('The Permit Application Title', $application->title);

        $this->assertTrue($repo->delete($application->remoteRef()));

    }
}

