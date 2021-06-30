<?php
namespace Jlab\EpasRepository\Model;

use Illuminate\Support\Str;
use Jlab\EpasRepository\Exception\ModelException;

abstract class BaseModel implements DocumentInterface
{
    /**
     * The ePAS Application data attributes.
     * @see Epas Service API documentation for list and definitions.
     * @var array
     */
    protected $data = [];


    function __construct(array $data){
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Returns an array representation of the object
     *
     * @return string
     */
    function toArray() : array {
        return $this->data;
    }

    /**
     * @inheritDoc
     *
     * @return string|null
     */
    function remoteRef(): ?string {
        return array_key_exists('RemoteRef', $this->data) ? $this->data['RemoteRef'] : null;
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    function surpassRef(): string
    {
        return $this->surpassRef;
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    function title(): string
    {
        return $this->title;
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
