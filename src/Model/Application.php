<?php
namespace Jlab\EpasRepository\Model;

/**
 * Class Application
 *
 * An ePAS Permit Application.
 */
class Application extends BaseModel
{
    function url() : string {
        return config('epas-repository.webApplication').'?ApplicationID='.$this->SurpassRef;
    }

}
