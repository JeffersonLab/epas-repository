<?php

namespace Jlab\EpasRepository\Model;


class Permit extends BaseModel
{

    function url(): string {
        return config('epas-repository.webPermit').'?PermitID='.$this->SurpassRef;
    }

    /**
     * @return string
     */
    function documentNumber(): string
    {
        return $this->permitNumber;
    }

    /**
     * @return string
     */
    function typeName(): string
    {
        return $this->permitTypeName;
    }

    /**
     * @return string
     */
    function stateName(): string
    {
        return $this->permitStateName;
    }

    /**
     * @return string
     */
    function surpassRef(): string
    {
        return $this->surpassRef;
    }

    /**
     * @return string
     */
    function title(): string
    {
        return $this->title;
    }

}
