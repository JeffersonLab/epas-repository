<?php

namespace Jlab\EpasRepository\Repository;

use Jlab\EpasRepository\Model\Permit;

class PermitRepository extends EpasRepository
{

    public function __construct()
    {
        parent::__construct();
        $this->initApiClient(config('epas-repository.permitWsdl'));
    }


    /**
     * Retrieve ePAS permits related to the specified work order number.
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
        $retrieved = $this->call('GetPermitsByWorkOrderNumber', $params);
        return $this->collect($this->ParseMultipleResultsData($retrieved));
    }


    /**
     * @inheritDoc
     * @param array $data
     * @return Permit
     */
    protected function makeModel(array $data){
        return new Permit($data);
    }

}
