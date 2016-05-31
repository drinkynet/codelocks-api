<?php
/**
 * Define an abstract API method class that all API methods inherit from
 */

namespace drinkynet\Codelocks\Methods;

use \drinkynet\Codelocks\Codelocks as Codelocks;

abstract class ApiMethod
{
    /**
     * The API connection to use
     *
     * @var \drinkynet\Codelocks\Codelocks
     */
    protected $api;

    /**
     * The method path to execute
     * @var string
     */
    protected $method = '';

    /**
     * The parameters to send
     * @var array
     */
    protected $args = [];

    /**
     * A list of arguments that must exist before execute can be called
     * @var array
     */
    protected $requiredArgs = [];

    /**
     * Constructor
     * @param \drinkynet\Codelocks\Codelocks $api An initialised codelocks API connector
     */
    public function __construct(Codelocks $api)
    {
        $this->api = $api;
    }

    /**
     * Check that the required arguments for the method call are set
     *
     * Override if the test strategy is different
     *
     * @throws \Exception
     */
    protected function testRequired()
    {
        // If requiredArgs is empty no arguments are required
        if (empty($this->requiredArgs)) {
            return;
        }

        // Otherwise check that a key exists in the args array for each item
        // in the required array
        foreach ($this->requiredArgs as $arg) {
            if (!isset($this->args[$arg])) {
                throw new \Exception("Required argument \"$arg\" is missing");
            }
        }
    }

    /**
     * Execute the method call against the API and get the result
     *
     * Override if the execution steps are different
     *
     * @throws \Exception
     * @return API response
     */
    protected function execute()
    {
        // Check that all required arguments have been set
        $this->testRequired();

        if ($this->method == '') {
            throw new \Exception("Method call path is not defined");
        }
        return $this->api->get($this->method, $this->args);
    }

    /**
     * Execute the method call and return a processed result
     *
     * Must be implemented in extending classes because required result
     * processing will vary depending on the method call
     */
    abstract protected function get();
}
