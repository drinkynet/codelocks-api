<?php

namespace drinkynet\Codelocks;

use drinkynet\Codelocks\Methods as Methods;

class Codelocks
{
    /**
     * Store the key used to authenticate against the API
     *
     * @var string
     */
    private $key;

    /**
     * Store the access key used for some methods
     */
    private $accessKey;

    /**
     * Store the path to the API
     *
     * @var string
     */
    private $endpoint = 'https://api-connect.codelocks.io';

    /**
     * Store the prefix for netcode endpoints
     *
     * @var string
     */
    private $endpointNetcodePrefix = 'n';

    /**
     * Store the prefix for K3Connect endpoints
     *
     * @var string
     */
    private $endpointUtilityPrefix = 'u';

    /**
     * Store the endpoint version number prefix
     *
     * @var integer
     */
    private $endpointVersion = 1;

    /**
     * Validate the server certificate
     *
     * @var boolean
     */
    private $verifySSL = true;

    /**
     * Store information about the last request
     *
     * @var  array
     */
    private $lastRequest;

    /**
     * Store information about the last response
     *
     * @var array
     */
    private $lastResponse;

    /**
     * Store info about the last error
     *
     * @var mixed
     */
    private $lastError;

    /**
     * @param string $key The API key to use
     * @param string $accessKey The pairing ID to use
     */
    public function __construct($key, $accessKey)
    {
        // Check that the API key is of the right form and at least 40
        // characters
        if (!preg_match('/^[0-9a-zA-Z]{40,}$/', $key)) {
            throw new \Exception('Invalid API key');
        }

        // Check that the accessKey is of the right form and at least 10
        // characters
        if (!preg_match('/^[0-9a-z]{10,}$/', $accessKey)) {
            throw new \Exception('Invalid API access key');
        }

        $this->key = $key;
        $this->accessKey = $accessKey;
    }

    /**
     * Get the current endpoint address
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getEndpointNetcodePrefix()
    {
        return $this->endpointNetcodePrefix;
    }

    public function getEndpointUtilityPrefix()
    {
        return $this->endpointUtilityPrefix;
    }

    public function getEndpointVersion()
    {
        return $this->endpointVersion;
    }

    /**
     * Get the endpoint for a given method
     *
     * @param string $method
     *
     * @return string
     */
    public function endpointForMethod($method)
    {
        $prefix = '';
        // Netcode uses its own prefix, all other endpoints are on the utility prefix
        if ('netcode' === substr($method, 0, 7)) {
            $prefix = $this->endpointNetcodePrefix;
        } else {
            $prefix = $this->endpointUtilityPrefix;
        }

        return sprintf(
            '%s/%s/%s/%s',
            $this->endpoint,
            $prefix,
            $this->endpointVersion,
            $method
        );
    }

    /**
     * Overwrite the default endpoint address
     *
     * @param string $endpoint
     *
     * @return $this           Allow method chaining
     */
    public function setEndpoint($endpoint)
    {
        if (!is_null($endpoint)) {
            $this->endpoint = rtrim($endpoint, '/');
        }
        return $this;
    }

    /**
     * Overwrite the default endpoint prefixes
     *
     * @param string $type   The endpoint type to set
     * @param string $prefix The prefix to set
     *
     * @return $this         Allow method chaining
     */
    public function setEndpointPrefix($type, $prefix)
    {
        switch ($type) {
            case 'netcode':
                $this->endpointNetcodePrefix = $prefix;
                break;
            case 'utility':
                $this->endpointUtilityPrefix = $prefix;
                break;
        }

        return $this;
    }

    /**
     * Overwrite the default endpoint version
     *
     * @param numeric $version The endpoint version to set
     *
     * @return $this           Allow method chaining
     */
    public function setEndpointVersion($version)
    {
        $this->endpointVersion = $version;
        return $this;
    }

    /**
     * Make a request to the API
     *
     * @param  string         $method  The method to request
     * @param  array         $args    An optional array of arguments to pass to
     *                                the method
     * @param  string        $verb    The HTTP verb to use for the request
     * @param  integer       $timeout An optional timeout in seconds
     * @return array|boolean          An array of data from the API or true on
     *                                success, false on failure
     */
    private function request($method, $args = array(), $timeout = 10, $verb = 'GET')
    {
        // If cURL isn't available throw an exception
        if (!function_exists('curl_init') || !function_exists('curl_setopt')) {
            throw new \Exception("cURL functions are required but could not be found.");
        }

        //$url = $this->endpoint . '/' . $method;
        $url = $this->endpointForMethod($method);

        // Reset the last error and response
        $this->lastError = null;
        $this->lastResponse = array('headers' => null, 'body' => null);

        $this->lastRequest = array(
            'method' => $method,
            'verb' => $verb,
            'args' => $args,
            'timeout' => $timeout,
            'url' => $url,
            );

        $ch = curl_init();

        // Generate the auth header from the API key
        $authHeader = 'x-api-key: ' . $this->key;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
            $authHeader
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Drinkynet/Codelocks-API/7.0 (github.com/drinkynet/codelocks-api)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        switch ($verb) {
            case 'GET':
                $query = http_build_query($args);
                curl_setopt($ch, CURLOPT_URL, $url.'?'.$query);
                break;
        }

        $response = curl_exec($ch);
        $headers = curl_getinfo($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        $this->lastResponse = array(
            'headers' => $headers,
            'body' => $response,
            );

        // Decode the response
        $response = $response ? $this->decode($response, $contentType) : true;

        // If the status code isn't a success code its an error
        if (!in_array($status, [200, 201]) || $this->responseIsError($response)) {
            $this->lastError = curl_error($ch);
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Check the response for error conditions that look like success
     * @param  array   $respnose A decoded response
     * @return boolean           True if the response contains an error, false
     *                           if it does not
     */
    private function responseIsError($response)
    {
        if (!is_array($response)) {
            return false;
        }

        // Test for: {"error": "", "message": ""}
        if (isset($response['status']) && $response['status'] === 'error') {
            return true;
        }

        // Test for: {"netcode": "ERROR", "status": "ok"}
        if (isset($response['netcode']) && $response['netcode'] === 'ERROR') {
            return true;
        }

        return false;
    }

    /**
     * Decode an API result based on its type
     *
     * @param  mixed  $result      Data returned from the API
     * @param  string $contentType A MIME type
     * @return mixed
     */
    private function decode($result, $contentType)
    {
        switch ($contentType) {
            case 'application/json':
                $result = json_decode($result, true);
                break;
        }

        return $result;
    }

    /**
     * Perform a get request against the API
     *
     * @param  string        $method
     * @param  array         $args
     * @param  integer       $timeout
     * @return array|boolean
     */
    public function get($method, array $args = array(), $timeout = 10)
    {
        return $this->request($method, $args, $timeout, 'GET');
    }

    /**
     * Get information about the last request
     * @return array
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Get information about the last response
     * @return array
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Get information about the last error
     * @return string  Output from curl_error()
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * API Method calls
     */

    /**
     * Create a new duration method object
     *
     * @return Methods\Duration
     */
    public function duration()
    {
        return new Methods\Duration($this);
    }

    /**
     * Create a new durations method object
     *
     * @return Methods\Durations
     */
    public function durations()
    {
        return new Methods\Durations($this);
    }

    /**
     * Create a new init method object
     *
     * Returns the initialisation information for a lock model
     *
     * @return Methods\Init An instance of an Init object wiht the API
     *                      already injected
     */
    public function init()
    {
        return new Methods\Init($this);
    }

    /**
     * Create  a new K3connect method object
     *
     * Returns a list of locks for this set of API credentials and access key
     *
     * @param  string $accessKey An optional access key associated with this API
     *                           key (can be set later)
     * @return Methods\K3connect An instance of a lock object with the API
     *                           already injected
     */
    public function k3connect($accessKey = null)
    {
        $accessKey = is_null($accessKey) ? $this->accessKey : $accessKey;
        return new Methods\K3connect($this, $this->accessKey);
    }

    /**
     * Create a new netcode object
     *
     * Allows the creation of netcode objects for different locks from one
     * instance of codelocks
     *
     * @param  string $lockId    An optional lock ID (this can be set later)
     * @param  string $accessKey An optional access key associated with this API
     *                           key (can be set later)
     * @return Netcode        An instance of a Netcode object with the API
     *                        already injected
     */
    public function netcode($lockId = null)
    {
        $netcode = new Methods\Netcode($this, $lockId);
        if (isset($this->accessKey)) {
            $netcode->accessKey($this->accessKey);
        }
        return $netcode;
    }

    /**
     * @deprecated 5.0 Use the k3connect method instead. This method is
     * maintained for backwards compatability
     *
     * Create  a new lock method object
     *
     * Returns a list of locks for this set of API credentials and access key
     *
     * @param  string $accessKey An optional access key associated with this API
     *                           key (can be set later)
     * @return Methods\Lock      An instance of a lock object with the API
     *                           already injected
     */
    public function lock($accessKey = null)
    {
        $accessKey = is_null($accessKey) ? $this->accessKey : $accessKey;
        return new Methods\Lock($this, $this->accessKey);
    }
}
