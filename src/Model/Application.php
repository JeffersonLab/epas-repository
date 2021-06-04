<?php

namespace Jlab\EpasRepository\Model;

/**
 * Class Application
 *
 * An ePAS Permit Application.
 */
class Application extends BaseModel
{

    /**
     * The ePAS Application data attributes.
     *
     * The WSDL identifies many fields as minOccurs=1 which means that the attribute must be present but
     * need not have a value.
     *
     * A complication however is that the SOAP RFC does not allow null for int values
     * which is what many of the minOccurs=1 attributes are.  Inspecting the data that ePAS returns, it
     * appears that it internally uses "-2147483648" as something akin to null for integers.
     *
     *
     * @see ePAS API documentation for list and definitions.
     * @var array
     */
    protected $data = [
//        "RemoteRef" => '',
//        "SurpassRef" => "",
//        "CreatedByRemoteRef" => '',
//        "ResponsibilityGroupName" => "",
//        "ResponsibilityGroupID" => "",
//        "ApplicationTypeID" => "",
//        "ApplicationTypeName" => "",
//        "ApplicationStateName" => "",
//        "ApplicationStateID" => "",
//        "ApplicationNumber" => "",
//        "Title" => "",
//        "SpecialRequirements" => '',
//        "WorkOrderNumber" => "",
//        "OutageID" => "",
//        "OutageName" => '',
//        "WorkGroupName" => "",
//        "WorkGroupID" => "",
//        "Notes" => '',
//        "PlantItemRemoteRef" => "",
//        "AreaName" => '',
//        "TrainName" => "",
//        "TrainID" => "",
//        "UnitName" => '',
//        "DirectionalityID" => "",
//        "DirectionalityName" => '',
//        "ApplicationRecipientRemoteRef" => '',
//        "LastUpdatedDate" => "",
//        "ProjectAreaDetails_Complete" => "",
//        "ApplicantDetails_Complete" => "",
//        "Approvals" => '',
//        "AdditionalTextColumn1" => '',
//        "AdditionalTextColumn2" => '',
//        "AdditionalTextColumn3" => '',
//        "AdditionalTextColumn4" => '',
//        "AdditionalTextColumn5" => '',
//        "AdditionalTextColumn6" => '',
//        "AdditionalTextColumn7" => "",
//        "AdditionalTextColumn8" => '',
//        "AdditionalTextColumn9" => '',
//        "AdditionalTextColumn10" => '',
//        "AdditionalTextColumn11" => '',
//        "AdditionalTextColumn12" => '',
//        "PrimaryPlantItemID" => "",
//        "HIRAID" => "",
//        "PermitTypeID" => "",
//        "PlantItems" => '',
//        "ResidualRiskConcernID" => "",
//        "ResidualRiskConsequenceID" => "",
//        "ResidualRiskLikelihoodID" => "",
//        "ResidualRiskRatingID" => "",
//        "ApplicationWorkflowTimeline" => '',
//        "ApplicationRecipients" => '',
//        "ApplicationWorkScopes" => '',
//        "ApplicationDocs" => '',
//        "WorkCategories" => '',
//        "Hazards" => '',
//        "Controls" => ''
    ];

    function url(): string
    {
        return config('epas-repository.webApplication') . '?ApplicationID=' . $this->SurpassRef;
    }

    /**
     * @return string
     */
    function documentNumber(): string
    {
        return $this->applicationNumber;
    }

    /**
     * @return string
     */
    function typeName(): string
    {
        return $this->applicationTypeName;
    }

    /**
     * @return string
     */
    function stateName(): string
    {
        return $this->applicationStateName;
    }




}
