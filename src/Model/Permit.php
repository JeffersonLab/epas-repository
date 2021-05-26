<?php

namespace Jlab\EpasRepository\Model;


class Permit extends BaseModel
{

    function url(): string {
        return config('epas-repository.webPermit').'?PermitID='.$this->SurpassRef;
    }


}
