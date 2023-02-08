<?php

namespace Jlab\EpasRepository\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jlab\EpasRepository\Exception\WebServiceException;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use Jlab\EpasRepository\Exception\ConfigurationException;

use SoapClient;

/**
 * Class EpasRepository
 * @package Jlab\EpasRepository
 */
abstract class EpasRepository
{
    /**
     * Client instance for interacting with API.
     *
     * @var \RicorocksDigitalAgency\Soap\Request\Request
     */
    protected $apiClient;

    /**
     * Whether to trace and log XML of API interactions.
     *
     * @var boolean
     */
    public $trace = false;

    /**
     * The SOAP wsdl to use for API.
     *
     * @var string
     */
    protected $wsdl;

    /**
     * @var Collection
     */
    public $warnings;

    /**
     * @var mixed
     */
    protected $lastResponse;

    /**
     * EpasRepository constructor.
     *
     * @throws ConfigurationException if missing config data
     */
    function __construct(){
        $this->warnings = new Collection();
    }

    /**
     * Prepare the SOAP Client to make a call.
     *
     * @throws ConfigurationException
     */
    protected function initApiClient(){
        $this->apiClient = Soap::to($this->wsdl());
    }

    /**
     * Enables tracing and logging of API XML interactions.
     */
    function trace(){
        $this->trace = true;
        Soap::trace();
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
    function call(string $method, array $params = [], bool $withAuth = true)
    {
        $this->initApiClient();

        if ($withAuth) {
            $params = $this->authParam() + $params;
        }

        if ($this->trace) {             // log extra debug info
            $result = $this->callWithTrace($method, $params);
        }else{
            $result = $this->apiClient->call($method, $params);
        }

        $this->lastResponse = $result;

        return $this->resultData($method, $result);
    }

    /**
     * Call ePAS API method and log the XML messages used in the interaction.
     *
     * @param $method
     * @param $params
     * @return mixed
     */
    protected function callWithTrace($method, $params){
        $result = $this->apiClient->trace()->call($method, $params);

        if ($this->trace) {
            Log::debug($this->apiClient->getEndpoint());
            Log::debug(serialize($result->trace()->xmlRequest));
            Log::debug(serialize($result->trace()->xmlResponse));
        }

        return $result;
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
        if (! $this->wsdl){
            $this->wsdl = $this->integrationUrl().'/'.$this->webServiceName().'?wsdl';
        }
        return $this->wsdl;
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

    /**
     * Throws an exception if not web services url has been set.
     * @throws ConfigurationException
     */
    protected function assertWebServiceUrlIsSet(){
        if (! config('epas-repository.webServices')){
            throw new ConfigurationException('EPAS_WEB_SERVICES environment variable is not set');
        }
    }

    /**
     * Throw an exception if no API username has been specified.
     * @throws ConfigurationException
     */
    protected function assertApiUserNameIsSet(){
        if (! config('epas-repository.userName')){
            throw new ConfigurationException('EPAS_API_USER_NAME environment variable is not set');
        }
    }

    /**
     * Throw an exception if no API Token has been specified.
     * @throws ConfigurationException
     */
    protected function assertApiAuthTokenIsSet(){
        if (! config('epas-repository.authToken')){
            throw new ConfigurationException('EPAS_API_AUTH_TOKEN environment variable is not set');
        }
    }

    /**
     * The expected name of the responseAttribute.
     *
     * The API response from ePAS seems to be garbed in an XML wrapper that is passwd on
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
     * Convert string containing xml from an ePAS API call response into a multi-level array.
     *
     * Useful for parsing responses such as GetApplicationsByWorkOrderNumber which returns
     * data for a multiple Application entities.
     *
     * @param $string
     * @return array
     */
    protected function ParseMultipleResultsData($string)
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
     * Convert string containing xml from an ePAS API call response into single level array.
     *
     * Useful for parsing responses such as GetApplicationResponse which return data for a single
     * Application entity.
     *
     * @param $string
     * @return array
     */
    protected function ParseSingleResultData($string)
    {
        $xml = simplexml_load_string($string);
        $json = json_encode($xml);
        return json_decode($json, true);
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
                if ($result->ResultCode == 2){ // success with warnings
                    $this->saveWarnings($response->response->{$this->responseName($method)}->ResultData);
                }
                return true;
            }
            throw new WebServiceException($result->ResultText);
        }
        throw new WebServiceException('Missing or Invalid Web Service Response');
    }

    protected function saveWarnings($resultData){
       foreach ($this->ParseMultipleResultsData($resultData) as $warning){
           $this->warnings->push($warning);
       }
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
     * Extract the ResultText payload from the API Response.
     *
     * Can return null
     * @param string $method The API method that generated the response
     * @param Object $result The API result object
     * @return mixed|null
     * @throws WebServiceException
     */
    protected function resultText(string $method, $result)
    {
        $this->assertApiResponseIsGood($method, $result);
        if (isset($result->response->{$this->responseName($method)}->ResultText)){
            return $result->response->{$this->responseName($method)}->ResultText;
        }
        return null;
    }

    /**
     * Extract the ResultData payload from the API Response.
     *
     * Can return null
     * @param string $method The API method that generated the response
     * @param Object $result The API result object
     * @return mixed|null
     * @throws WebServiceException
     */
    protected function resultData(string $method, $result)
    {
        $this->assertApiResponseIsGood($method, $result);
        if (isset($result->response->{$this->responseName($method)}->ResultData)){
            return $result->response->{$this->responseName($method)}->ResultData;
        }
        return null;
    }

}
