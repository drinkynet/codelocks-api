<?php

namespace drinkynet\Codelocks;

class Netcode
{
    /**
     * The API connection to use
     *
     * @var \drinkynet\Codelocks\Codelocks
     */
    private $api;

    /**
     * The ID of the lock to generate a code for
     *
     * @var string
     */
    private $lockId;

    /**
     * The date to generate a code for
     *
     * @var string
     */
    private $date;

    /**
     * The hour to generate a code for
     *
     * @var string
     */
    private $hour;

    /**
     * The duration ID to send
     *
     * @var integer
     */
    private $duration;

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

    private function execute()
    {
        $method = 'netcode/ncgenerator/getnetcode';

        $args = array(
            'id' => $this->lockId,
            'sd' => $this->date,
            'st' => $this->hour,
            'du' => $this->duration,
            );

        return $this->api->get($method, $args);
    }

    /**
     * Set configuration defaults to now
     */
    protected function setDefaults()
    {
        $now = new \DateTime('NOW');
        $this->date($now);
        $this->hour($now->format('H'));
        $this->duration(0);
    }

    /**
     * Set the lock ID
     *
     * @param  string $lockId The ID of the lock to generate a code for
     * @return $this          Allow method chaining
     */
    public function lock($lockId)
    {
        $this->lockId = $lockId;
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
        $this->date = $date->format('d/m/Y');
        return $this;
    }

    /**
     * Set the hour
     *
     * @param  mixed     $hour The hour to generate a code for
     * @throws Exception If the hour value is out of range
     * @return $this     Allow method chaining
     */
    public function hour($hour)
    {
        $hour = (int) $hour;

        // Check that the hour is within the correct range
        if ($hour < 0 || $hour > 23) {
            throw new Exception('Hour must be an integer between 0 and 23');
        }

        // API requires a two character string for hours
        $this->hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
        return $this;
    }

    /**
     * Convert a time in hours to a codelocks duration ID
     *
     * @param  integer $duration A duration in hours
     * @return $this             Allow method chianing
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

        $this->duration = $d;

        return $this;
    }

    /**
     * Request a netcode form the API
     *
     * @return string A netcode
     */
    public function get()
    {
        $result = $this->execute();
        return isset($result['netcode']) ? $result['netcode'] : false;
    }
}
