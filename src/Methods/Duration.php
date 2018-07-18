<?php
/**
 * @file(Duration.php)
 *
 * Get details about the
 */

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class Duration extends ApiMethod
{
    protected $method = 'duration';

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['lockmodel'];

    /**
     * Get the data from the API
     * @return Array
     * @throws Exception If the duration ID was not set
     */
    public function get()
    {
        // If the duration ID has not been appended to the method path
        // throw an exception
        if ($this->method === 'duration') {
            throw new \Exception("Duration ID not set", 1);
        }

        $result = $this->execute();
        return $result;
    }

    /**
     * Parameter config functions
     */

    /**
     * Specify the duration ID to get detail for
     * @param  integer $durationId The ID of the duration to get detail for
     * @return this                Allow method chaining
     */
    public function durationId($durationId)
    {
        $this->method = sprintf('duration/%s', $durationId);
        return $this;
    }

    /**
     * Set the lock model for this durations call
     * @param  string $lockModel The model identifier to request durations for
     * @return this              Allow method chaining
     */
    public function lockModel($lockModel)
    {
        $this->args['lockmodel'] = $lockModel;
        return $this;
    }
}
