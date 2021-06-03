<?php
namespace Jlab\EpasRepository\Model;

use Illuminate\Support\Str;
use Jlab\EpasRepository\Exception\ModelException;

abstract class BaseModel
{
    /**
     * The ePAS Application data attributes.
     * @see ePAS API documentation for list and definitions.
     * @var array
     */
    protected $data = [];


    function __construct(array $data){
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Returns the url to viewing the model in ePAS.
     *
     * @return string
     */
    abstract function url() : string;

    /**
     * Returns an array representation of the object
     *
     * @return string
     */
    function toArray() : array {
        return $this->data;
    }


    /**
     * Magic method allowing access to model attributes.
     * @param $attr
     * @return mixed
     * @throws ModelException
     */
    public function __get($attr){
        if (array_key_exists(Str::studly($attr), $this->data)){
            return $this->data[Str::studly($attr)];
        }
        throw new ModelException("Invalid model attribute: $attr");
    }

    /**
     * Magic method allowing access to model attributes.
     * @param $attr
     * @return void
     * @throws ModelException
     */
    public function __set($attr, $val){
        if (array_key_exists(Str::studly($attr), $this->data)){
            $this->data[Str::studly($attr)] = $val;
        }else{
            throw new ModelException("Invalid model attribute: $attr");
        }
    }


}
