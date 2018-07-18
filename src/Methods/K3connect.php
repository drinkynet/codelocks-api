<?php
/**
 * API locks method
 *
 * Returns a list of locks accociated with the API credentials
 */

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class K3connect extends ApiMethod
{
    protected $method = 'k3connect';

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

    /**
     * Get a list of locks
     * @param  string $lockId Optional lock ID to return just a single lock
     * @return Object         Result object
     */
    public function get($lockId = null)
    {
        if ($lockId !== null) {
            $this->lockId($lockId);
        }

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

    /**
     * Specify a single lock to return instead of the default list of all locks
     * @param  string $lockId Optional lock ID to return just a single lock
     * @return this
     */
    public function lockId($lockId)
    {
        $this->method = sprintf('k3connect/%s', $lockId);
        return $this;
    }
}
