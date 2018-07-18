<?php
/**
 * @file(Durations.php)
 *
 * Returns a list of durations
 */

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class Durations extends ApiMethod
{
    protected $method = 'durations';

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['lockmodel'];

    public function get()
    {
        // DURATIONS appears to be the only valid option for this argument
        // so we default to it if not otherwise set
        if (!isset($this->args['operation'])) {
            $this->operation('DURATIONS');
        }
        $result = $this->execute();
        return is_array($result) ? $result : false;
    }

    /**
     * Parameter config functions
     */

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

    /**
     * Set the operation for this durations call
     * @param  string $lockModel The model identifier to request durations for
     * @return this              Allow method chaining
     */
    public function operation($operation)
    {
        $this->args['operation'] = $operation;
        return $this;
    }

    /**
     * Set the query parameter for this durations call
     *
     * Note: [Deprecated] Replaced by lockmodel - maintained for backwards
     *       compatibility. Future use may not be supported.
     *
     * @param  string $lockModel The model identifier to request durations for
     * @return this              Allow method chaining
     */
    public function query($query)
    {
        $this->args['query'] = $query;
        return $this;
    }
}
