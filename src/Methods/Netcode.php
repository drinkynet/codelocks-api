<?php

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class Netcode extends ApiMethod
{
    /**
     * Store the start dateTime for the code window
     * @var \DateTime
     */
    private $start;

    /**
     * Define the required arguments for this method call
     * @var array
     */
    protected $requiredArgs = ['start', 'duration', 'lockmodel', 'identifier'];

    /**
     * @param Codelocks $api
     * @param string    $lockId Optional ID of the lock to use
     */
    public function __construct(Codelocks $api, $lockId = null)
    {
        $this->api = $api;

        $this->lock($lockId);

        $this->setDefaults();
    }

    /**
     * Set configuration defaults to now
     */
    protected function setDefaults()
    {
        $now = new \DateTime('NOW');
        $this->start($now);
        $this->duration(0);
        $this->lockModel('K3CONNECT');
    }

    protected function execute()
    {
        // Set the start time
        $this->args['start'] = $this->start->format('Y-m-d H:i');

        // Check that all required arguments have been set
        parent::testRequired();

        if ($this->method == '') {
            throw new \Exception("Method call path is not defined");
        }
        return $this->api->get($this->method, $this->args);
    }

    /**
     * Request a netcode form the API
     *
     * @return string A netcode
     */
    public function get()
    {
        $result = $this->execute();
        return isset($result['ActualNetcode']) ? $result['ActualNetcode'] : false;
    }

    /**
     * Parameter config functions
     */

    /**
     * Set the access key for this request
     *
     * Note: The access key must be sent with the variable name 'identifier'
     *       for this method call
     *
     * @param  string $accessKey The access key associated with the API key
     * @return $this              Allow method chaining
     */
    public function accesskey($accessKey)
    {
        $this->args['identifier'] = $accessKey;
        return $this;
    }

    /**
     * Set the lock ID
     *
     * @param  string $lockId The ID of the lock to generate a code for
     * @return $this          Allow method chaining
     */
    public function lock($lockId)
    {
        // The method path is dynamic because it includes the lock ID so we set
        // it here
        $this->method = 'netcode/' . $lockId;
        return $this;
    }

    /**
     * Set the lock model for this lock
     * @param  string $lockModel The model identifier for this lock type e.g.:
     *                           K3CONNECT
     * @return $this             Allow method chaining
     */
    public function lockModel($lockModel)
    {
        $this->args['lockmodel'] = $lockModel;
        return $this;
    }

    /**
     * Set the start date time
     * @param  \DateTime $start A dateTime object representing the start of the
     *                          code window
     * @return $this             Allow method chaining
     */
    public function start(\DateTime $start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * Set the date
     *
     * @param  \DateTime $date The date to generate a code for
     * @return $this           Allow method chaining
     */
    public function date(\DateTime $date)
    {
        $this->start->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        return $this;
    }

    /**
     * Set the hour
     *
     * @param  mixed     $hour The hour to generate a code for
     * @throws \Exception If the hour value is out of range
     * @return $this     Allow method chaining
     */
    public function hour($hour)
    {
        $hour = (int) $hour;

        // Check that the hour is within the correct range
        if ($hour < 0 || $hour > 23) {
            throw new \Exception('Hour must be an integer between 0 and 23');
        }

        // API requires a two character string for hours
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);

        $this->start->setTime($hour, 0, 0);
        return $this;
    }

    /**
     * Convert a time in hours to a codelocks duration ID
     *
     * @param  integer $duration A duration in hours
     * @return $this             Allow method chaining
     */
    public function duration($d)
    {
        $d = (int) $d;

        switch ($d) {
            // 1 hour
            case 0:
                $d = 0;
                break;

            // 1 - 12 hours
            case in_array($d, range(1, 12)):
                $d = $d - 1;
                break;

            // 1 day
            case in_array($d, range(13, 24)):
                $d = 12;
                break;

            // 2 days
            case in_array($d, range(25, 48)):
                $d = 13;
                break;

            // 3 days
            case in_array($d, range(49, 72)):
                $d = 14;
                break;

            // 4 days
            case in_array($d, range(73, 96)):
                $d = 15;
                break;

            // 5 days
            case in_array($d, range(97, 120)):
                $d = 16;
                break;

            // 6 days
            case in_array($d, range(121, 144)):
                $d = 17;
                break;

            // 8 days
            default:
                $d = 18;
                break;
        }

        $this->args['duration'] = $d;

        return $this;
    }
}
