<?php
/**
 * API lock initialise method
 */

namespace drinkynet\Codelocks\Methods;

class Init extends ApiMethod
{
    protected $method = 'init';

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['lockmodel'];

    /**
     * Return the init sequence data for the specified lock model
     * @return array|false
     */
    public function get()
    {
        $result = $this->execute();
        return isset($result['InitSeq']) ? $result : false;
    }

    /**
     * Parameter config functions
     */

    /**
     * Set the lock model for this init call
     * @param  string $lockModel The model identifier to request an init routine for
     * @return this              Allow method chaining
     */
    public function lockModel($lockModel)
    {
        $this->args['lockmodel'] = $lockModel;
        return $this;
    }

    /**
     * Set the master code for this init call
     * @param  string $masterCode The master code will be used to generate the init sequence
     * @return this               Allow method chaining
     */
    public function masterCode($masterCode)
    {
        $this->args['mastercode'] = $masterCode;
        return $this;
    }
}
