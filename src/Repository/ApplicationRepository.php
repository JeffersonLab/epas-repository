<?php


namespace Jlab\EpasRepository\Repository;

use Jlab\EpasRepository\Model\Application;
use RicorocksDigitalAgency\Soap\Facades\Soap;

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
        return $this->collect($this->ParseMultipleResultsData($retrieved));
    }

    /**
     * Retrieve an ePAS Application using its RemoteRef key.
     *
     * The RemoteRef key is sent by the client when it creates an Application via
     * the API.  The ePAS server enforces uniqueness, so it can be used to retrieve
     * a single specific Application.
     *
     *
     * @param $remoteRef
     * @return Application
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    function getApplication($remoteRef){
        $params['strRemoteRef'] = $remoteRef;
        $retrieved = $this->call('GetApplication', $params);
        return new Application($this->ParseSingleResultData($retrieved));
    }

    /**
     * Save a Permit Application to ePAS
     */
    function save(Application $application){
        // Make the API call that should persist the data to ePAS database
        $retrieved = $this->callAddApplication($application);

        // Use the unique remoteRef property that we specified during AddApplication
        // to turn around and retrieve the newly created Application.
        return $this->getApplication($application->remoteRef());
    }

    /**
     * Special call method that can use locally modified wsdl.
     *
     * This is necessary right now because the ePAS wsdl online incorrectly specifies a bunch
     * of fields as required (minOccurs="1") even though they are not in fact required.
     * One workaround is to save a local copy of that wsdl where we can change those
     * unnecessary values to minOccurs="0".
     *
     * @param Application $application
     * @return mixed
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    protected function callAddApplication(Application $application){
        // Must init client to use a local WSDL copy with bogus minOccurs=1 items removed
        $this->initApiClient(config('epas-repository.applicationWsdl'));

        // Do the API call
        $params['sdoApplication'] = $application->toArray();
        $retrieved = $this->call('AddApplication', $params);

        // restore client back to default
        $this->initApiClient();

        // Return the results of the API call
        return $retrieved;
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
        // this call returns JSON.  Go figure.
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
