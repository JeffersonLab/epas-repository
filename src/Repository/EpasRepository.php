<?php

namespace Jlab\EpasRepository\Repository;

use Illuminate\Support\Collection;
use Jlab\EpasRepository\Exception\WebServiceException;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use Jlab\EpasRepository\Exception\ConfigurationException;

/**
 * Class EpasRepository
 * @package Jlab\EpasRepository
 */
abstract class EpasRepository
{
    /**
     * @var \RicorocksDigitalAgency\Soap\Request\Request
     */
    protected $apiClient;

    /**
     * EpasRepository constructor.
     *
     * @throws ConfigurationException if missing config data
     */
    function __construct(){
        $this->apiClient = Soap::to($this->wsdl());
    }

    /**
     * Call an ePAS API method.
     *
     * @param string $method API Method to call (ex: GetApplicationsByWorkOrderNumber)
     * @param array $params Parameters to pass to API method
     * @param bool $withAuth Include authentication data with request (default = true)
     * @return mixed
     * @throws \Jlab\EpasRepository\Exception\ConfigurationException
     */
    function call(string $method, array $params, bool $withAuth = true)
    {
        if ($withAuth) {
            $params = $this->authParam() + $params;
        }
        $result = $this->apiClient->call($method, $params);
        return $this->extractResultData($method, $result);
    }

    /**
     * Create an appropriate object model from the array data.
     *
     * @param array $data
     * @return mixed
     */
    abstract protected function makeModel(array $data);


    /**
     * Returns the URL of the SOAP wsdl to use.
     * @return string
     * @throws ConfigurationException
     */
    function wsdl() : string {
        return $this->integrationUrl().'/'.$this->webServiceName().'?wsdl';
    }

    /**
     * Return the name of the EPAS WebService to use.
     * @return string
     */
    protected function webServiceName(){
        return $this->modelName().'WebService.asmx';
    }

    /**
     * Returns the name of the model that the repository fetches.
     *
     * @return array|string|string[]|null
     */
    protected function modelName(){
        $classParts = explode('\\',get_called_class());
        $class = array_pop( $classParts);
        return str_replace('Repository','', $class);
    }

    /**
     * Return the URL for the EPAS Integration API
     * @return string
     */
    protected function integrationUrl(){
        $this->assertWebServiceUrlIsSet();
        return config('epas-repository.webServices').'/Integration';
    }

    /**
     * Returns the ePAS API authentication parameter that must be included with most calls.
     *
     * @throws ConfigurationException
     */
    public function authParam(){
        $this->assertApiUserNameIsSet() && $this->assertApiAuthTokenIsSet();
        return [
            'sdoAuthObject' => [
                'UserName' => config('epas-repository.userName'),
                'AuthKey' => config('epas-repository.authKey'),
            ]
        ];
    }


    protected function assertWebServiceUrlIsSet(){
        if (! config('epas-repository.webServices')){
            throw new ConfigurationException('EPAS_WEB_SERVICES environment variable is not set');
        }
    }

    protected function assertApiUserNameIsSet(){
        if (! config('epas-repository.userName')){
            throw new ConfigurationException('EPAS_API_USER_NAME environment variable is not set');
        }
    }

    protected function assertApiAuthTokenIsSet(){
        if (! config('epas-repository.authToken')){
            throw new ConfigurationException('EPAS_API_AUTH_TOKEN environment variable is not set');
        }
    }

    /**
     * The expected name of the responseAttribute.
     *
     * The API response from ePAS seems to be wrapped in an XML wrapper that is passwd on
     * the method that was called.  For example, the response to calling GetApplicationsByWorkOrderNumber
     * will be wrapped inside a GetApplicationsByWorkOrderNumberResult.
     *
     * @param $method
     * @return string
     */
    protected function responseName($method)
    {
        return $method . 'Result';
    }

    /**
     * Convert string containing xml from an ePAS API call response into an array of records.
     *
     * @param $string
     * @return array
     */
    protected function parseResultDataXml($string)
    {
        $data = [];
        $xml = simplexml_load_string($string);
        foreach ($xml->children() as $element){
            $json = json_encode($element);
            $data[] = json_decode($json, true);
        }
        return $data;
    }

    /**
     * Throws if API response is invalid or indicates an error.
     *
     * The ePAS convention is
     *      ResultCode >= 0: Success
     *      ResulteCode < 0: Error
     *
     * @param $method
     * @param $response
     * @return bool
     * @throws WebServiceException
     */
    protected function assertApiResponseIsGood($method, $response)
    {
        if (isset($response->response) && isset($response->response->{$this->responseName($method)})) {
            $result = $response->response->{$this->responseName($method)};
            if ($result->ResultCode >= 1) {
                return true;
            }
            throw new WebServiceException($result->ResultText);
        }
        throw new WebServiceException('Missing or Invalid Web Service Response');
    }

    /**
     * Convert array of API results data into a collection of objects.
     *
     * The type of object in the collection will be determined by the makeModel
     * method implementation.
     *
     * @param array $data
     * @return Collection
     */
    protected function collect(array $data)
    {
        $collection = new Collection();
        foreach ($data as $datum) {
            $collection->push($this->makeModel($datum));
        }
        return $collection;
    }

    /**
     * Extract the ResultData payload from the API Response.
     *
     * @param string $method The API method that generated the response
     * @param Object $result The API result object
     * @return mixed
     * @throws WebServiceException
     */
    protected function extractResultData(string $method, $result)
    {
        $this->assertApiResponseIsGood($method, $result);
        return $result->response->{$this->responseName($method)}->ResultData;
    }


}
