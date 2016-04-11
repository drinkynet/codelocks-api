<?php

namespace drinkynet\Codelocks;

class Codelocks
{
    /**
     * Store the key used to authenticate against the API
     *
     * @var string
     */
    private $key;

    /**
     * Store the pairing ID used to authenticate against the API
     *
     * @var string
     */
    private $pid;

    /**
     * Store the path to the API
     *
     * @var string
     */
    private $endpoint = 'https://api-2445581366752.apicast.io/api/v3';

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
     * @param string $pid The pairing ID to use
     */
    public function __construct($key, $pid)
    {
        // Check that the API key is of the right form
        if (!preg_match('/^[0-9a-f]{32}$/', $key)) {
            throw new \Exception('Invalid API key');
        }

        // Check that the Paring ID is of the right form
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $pid)) {
            throw new \Exception('Invalid Paring ID');
        }

        $this->key = $key;
        $this->pid = $pid;
    }

    /**
     * Make a request to the API
     *
     * @param  strng         $method  The method to request
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
            throw new Exception("cURL functions are required but could not be found.");
        }

        $url = $this->endpoint . '/' . $method;

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

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
        ));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Drinkynet/Codelocks-API/1.0 (github.com/drinkynet/codelocks-api)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        // user_key and pid are passed in args rather than headers so we add
        // them in here
        $args['user_key'] = $this->key;
        $args['pid'] = $this->pid;

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
     * Create a new netcode object
     *
     * Allows the creation of netcode objects for different locks from one
     * instance of codelocks
     *
     * @param  string $lockId An optional lock ID (this can be set later)
     * @return Netcode        An instance of a Netcode object with the API
     *                        already injected
     */
    public function netcode($lockId = null)
    {
        return new Netcode($this, $lockId);
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
}
