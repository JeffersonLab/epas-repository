<?php
namespace Jlab\EpasRepository\Tests;

use Illuminate\Support\Facades\Config;
use Jlab\EpasRepository\Model\Application;

class ApplicationTest extends TestCase
{
    const WEB_APPLICATION = 'https://epas-rk95-dev.staging.prometheusgroup.app/Application/General/Default.aspx';

    protected $application;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('epas-repository.webApplication', ApplicationTest::WEB_APPLICATION);
        $this->application = new Application([
            "RemoteRef" => [],
            "SurpassRef" => "11",
            "CreatedByRemoteRef" => [],
            "ResponsibilityGroupName" => "Accelerator",
            "ResponsibilityGroupID" => "29",
            "ApplicationTypeID" => "5",
            "ApplicationTypeName" => "Permit Request",
            "ApplicationStateName" => "PermitCreated",
            "ApplicationStateID" => "-2147483648",
            "ApplicationNumber" => "JLab-PR-10",
            "Title" => "Integration Test",
            "SpecialRequirements" => [],
            "WorkOrderNumber" => "ATLIS-20201",
            "OutageID" => "-2147483648",
            "OutageName" => [],
            "WorkGroupName" => "Accelerator",
            "WorkGroupID" => "116",
            "Notes" => [],
            "RequiredDateTime" => [],
            "PlantItemRemoteRef" => "FM-A-44-SHP-1",
            "AreaName" => [],
            "TrainName" => "Sample Area 1",
            "TrainID" => "141",
            "UnitName" => [],
            "DirectionalityID" => "-2147483648",
            "DirectionalityName" => [],
            "ApplicationRecipientRemoteRef" => [],
            "ExpectedReturnDateTime" => [],
            "LastUpdatedDate" => "0001-01-01T00:00:00",
            "ProjectAreaDetails_Complete" => "false",
            "ApplicantDetails_Complete" => "true"
        ]);
    }

    function test_it_provides_correct_url()
    {
        $this->assertEquals(ApplicationTest::WEB_APPLICATION.'?ApplicationID=11', $this->application->url());
    }


    function test_it_implements_abstract_methods(){
        $this->assertEquals('Permit Request', $this->application->typeName());
        $this->assertEquals('JLab-PR-10', $this->application->documentNumber());
        $this->assertEquals('PermitCreated', $this->application->stateName());
        $this->assertEquals('Integration Test', $this->application->title());
        $this->assertEquals('11', $this->application->surpassRef());
    }

    function test_it_handles_non_scalar_title(){
        $this->application->title = [];
        $this->assertIsArray($this->application->title);
        $this->assertIsScalar($this->application->title());
    }
}
