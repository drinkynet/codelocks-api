<?php
/**
 * API lock initialise method
 */

namespace drinkynet\Codelocks\Methods;

class Durations extends ApiMethod
{
    protected $method = 'durations';

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['query','operation'];

    /**
     * Return the init sequence data for the specified lock model
     * @return array|false
     */
    public function get()
    {
        $result = $this->execute();
        return $result;
    }

    /**
     * Parameter config functions
     */

    /**
     * Set the lock model for this init call
     * @param  string $query The model identifier to request an init routine for
     * @return this              Allow method chaining
     */
    public function query($query)
    {
        $this->args['query'] = $query;
        return $this;
    }

    /**
     * Set the operation for this durations call
     * @param  string $operation The operation will select the correct action
     * @return this               Allow method chaining
     */
    public function operation($operation)
    {
        $this->args['operation'] = $operation;
        return $this;
    }
}
