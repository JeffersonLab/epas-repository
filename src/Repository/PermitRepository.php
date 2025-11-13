<?php

namespace Jlab\EpasRepository\Repository;

use Illuminate\Support\Collection;
use Jlab\EpasRepository\Model\Permit;

class PermitRepository extends EpasRepository
{

    public function __construct()
    {
        parent::__construct();
        $this->wsdl = config('epas-repository.permitWsdl');
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
        try{
            $retrieved = $this->call('GetPermitsByWorkOrderNumber', $params);
            return $this->collect($this->ParseMultipleResultsData($retrieved));
        }catch(\Exception $e){
            // Unfortunately, we need to parse the exception text to see if
            // it's simply a matter of no records located and we can just
            // return an empty result set.
            if (stristr($e->getMessage(),'No permit record located')){
                return new Collection();  // empty result set
            }
            // re-throw any other exceptions
            throw $e;
        }
    }

  /**
   * Retrieve ePAS permits related to the specified state name.
   *
   * The ePAS StateName field is a string field that indicates the approval
   * or execution status of the permit.
   *
   * ex:  OnIssue  or PermitIssued
   *
   * @param $stateName
   * @return \Illuminate\Support\Collection
   * @throws \Jlab\EpasRepository\Exception\ConfigurationException
   */
  function findByState($stateName){
    $params['strPermitStateName'] = $stateName;
    try{
      $retrieved = $this->call('GetPermitsByState', $params);
      return $this->collect($this->ParseMultipleResultsData($retrieved));
    }catch(\Exception $e){
      // Unfortunately, we need to parse the exception text to see if
      // it's simply a matter of no records located and we can just
      // return an empty result set.
      if (stristr($e->getMessage(),'No permit record located')){
        return new Collection();  // empty result set
      }
      // re-throw any other exceptions
      throw $e;
    }
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
