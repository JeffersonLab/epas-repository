<?php
namespace Jlab\EpasRepository\Tests;

use Illuminate\Support\Facades\Config;
use Jlab\EpasRepository\Model\Permit;

class PermitTest extends TestCase
{
    const WEB_PERMIT = 'https://epas-rk95-dev.staging.prometheusgroup.app/Permit/General/Default.aspx';

    protected $permit;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('epas-repository.webPermit', PermitTest::WEB_PERMIT);
        $this->permit = new Permit([
            "RemoteRef" => [],
            "SurpassRef" => "11",
            "CreatedByRemoteRef" => [],
            "AreaName" => [],
            "DirectionalityID" => "-2147483648",
            "DirectionalityName" => [],
            "DisplayName" => "JLab-PTW-11",
            "PermitNumber" => "JLab-PTW-11",
            "PermitStateName" => "PlanningComplete",
            "PermitStateID" => "-2147483648",
            "PermitTypeID" => "3",
            "PermitTypeName" => "Permit To Work",
            "PlantItemRemoteRef" => "FM-A-44-SHP-1",
            "RequiredDateTime" => [],
            "LastUpdatedDate" => "0001-01-01T00:00:00",
            "ResponsibilityGroupName" => "Accelerator",
            "ResponsibilityGroupID" => "-2147483648",
            "SpecialRequirements" => [],
            "Title" => "Integration Test",
            "TrainName" => "Sample Area 1",
            "TrainID" => "141",
            "UnitName" => [],
            "WorkGroupName" => "Accelerator",
            "WorkGroupID" => "116",
            "WorkLocation" => [],
            "WorkOrderNumber" => "ATLIS-20201",
            "HIRAID" => "48",
            "CreatedBySurpassRef" => "-2147483648",
            "PermitMasterID" => "-2147483648",
            "Revision" => "-2147483648",
            "PlantWorkTextArea1" => [],
            "PermitRelationships" => [],
            "PermitQuestionAnswers" => [],
            "Approvals" => [],
        ]);
    }

    function test_it_provides_correct_url()
    {
        $this->assertEquals(PermitTest::WEB_PERMIT.'?PermitID=11', $this->permit->url());
    }

    function test_it_implements_abstract_methods(){
        $this->assertEquals('Permit To Work', $this->permit->typeName());
        $this->assertEquals('JLab-PTW-11', $this->permit->documentNumber());
        $this->assertEquals('PlanningComplete', $this->permit->stateName());
        $this->assertEquals('Integration Test', $this->permit->title());
        $this->assertEquals('11', $this->permit->surpassRef());
    }

}
