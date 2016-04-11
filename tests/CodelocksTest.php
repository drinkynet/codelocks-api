<?php

namespace drinkynet\Codelocks\Tests;

use \drinkynet\Codelocks\Codelocks;

class CodelocksTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Timezone must be set otherwise \DateTime will error on creation
        date_default_timezone_set('Europe/London');

        $envPath = __DIR__ . '/../';

        if (file_exists($envPath . '.env')) {
            $dotenv = new \Dotenv\Dotenv($envPath);
            $dotenv->load();
        }
    }

    public function testBadAPIKey()
    {
        $this->setExpectedException('\Exception');
        $codelocks = new Codelocks('012abc456', '00000000-0000-0000-0000-000000000000');
    }

    public function testBadPID()
    {
        $this->setExpectedException('\Exception');
        $codelocks = new Codelocks('00000000000000000000000000000000', '0123abc456');
    }

    /**
     * @covers Codelocks::__construct
     */
    public function testInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $pid = getenv('CODELOCKS_API_PAIRING_ID');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$pid) {
            $this->markTestSkipped('No CODELOCKS_API_PAIRING_ID in ENV');
        }

        $codelocks = new Codelocks($key, $pid);
        $this->assertInstanceOf('\drinkynet\Codelocks\Codelocks', $codelocks);
    }

    /**
     * @covers Codelocks::netcode
     */
    public function testNetcodeInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $pid = getenv('CODELOCKS_API_PAIRING_ID');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$pid) {
            $this->markTestSkipped('No CODELOCKS_API_PAIRING_ID in ENV');
        }

        $codelocks = new Codelocks($key, $pid);

        $netcode = $codelocks->netcode();

        $this->assertInstanceOf('\drinkynet\Codelocks\Netcode', $netcode);
    }
}
