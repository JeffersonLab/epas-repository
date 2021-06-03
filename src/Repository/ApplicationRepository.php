<?php


namespace Jlab\EpasRepository\Repository;

use Jlab\EpasRepository\Model\Application;

/**
 * Class ApplicationRepository
 *
 * Interacts with ePAS API to retrieve and store Permit Applications. (AKA Permit Requests)
 *
 * @package Jlab\EpasRepository
 */
class ApplicationRepository extends EpasRepository
{
    /**
     * Retrieve ePAS applications related to the specified work order number.
     *
     * The ePAS WorkOrderNumber field is a string field that contains a reference to
     * a record in some external work order generation system.  At Jlab, these WorkOrderNumbers
     * will be in the format {system}{id}.
     *
     * ex:  ATLIS-20201  or MAXIMO-1001
     *
     * @param $orderNumber
     * @return \Illuminate\Support\Collection
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    function findByWorkOrder($orderNumber){
        $params['strWorkOrderNumber'] = $orderNumber;
        $retrieved = $this->call('GetApplicationsByWorkOrderNumber', $params);
        return $this->collect($this->parseResultDataXml($retrieved));
    }

    /**
     * Save a Permit Application to ePAS
     */
    function save(Application $application){
        // Local WSDL copy with bogus minOccurs=1 items removed
        //$this->wsdl = 'http://localhost/epas/ApplicationWebService.asmx.xml';
        // Direct URL to the method:
        //return $this->integrationUrl().'/'.$this->webServiceName().'?op=AddApplication';
        $params['sdoApplication'] = $application->toArray();
        $retrieved = $this->call('AddApplication', $params);
        return $this->collect($this->parseResultDataXml($retrieved));
    }

    /**
     * Get a collection of available Application types
     *
     *  Example Application Type Record returned by ePAS API :
     *   "ApplicationTypeID": 5
     *   "Name": "Permit Request"
     *   "Abbreviation": "PR"
     *   "Color": "#EEEEEE"
     *
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    function applicationTypes(){
        $retrieved = $this->call('GetAllApplicationTypes');
        // Unlike many (most?) other ePAS API calls which return XML ResultData,
        // this call returns JSON.
        return collect(json_decode($retrieved));
    }

    /**
     * @inheritDoc
     * @param array $data
     * @return Application
     */
    protected function makeModel(array $data){
        return new Application($data);
    }

}
