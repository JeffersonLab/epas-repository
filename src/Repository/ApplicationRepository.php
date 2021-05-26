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
     * @inheritDoc
     * @param array $data
     * @return Application
     */
    protected function makeModel(array $data){
        return new Application($data);
    }

}
