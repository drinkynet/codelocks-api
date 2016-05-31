<?php
/**
 * API locks method
 *
 * Returns a list of locks accociated with the API credentials
 */

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class Lock extends ApiMethod
{
    protected $method = 'lock';

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['accesskey'];

    /**
     * Allow setting of the access key on the construction call
     * @param  Codelocks $api
     * @param  string    $accessKey
     */
    public function __construct(Codelocks $api, $accesskey = null)
    {
        $this->api = $api;
        $this->accesskey($accesskey);
    }

    public function get()
    {
        $result = $this->execute();
        return $result;
    }

    /**
     * Parameter config functions
     */

    /**
     * Set the access key
     * @param  string $accesskey [description]
     * @return this              Allow method chaining
     */
    public function accesskey($accesskey)
    {
        $this->args['accesskey'] = $accesskey;
        return $this;
    }
}
