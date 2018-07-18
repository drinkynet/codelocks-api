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
        $codelocks = new Codelocks('a-1', 'aaaaaaaaa0');
    }

    public function testBadAccessKey()
    {
        $this->setExpectedException('\Exception');
        $codelocks = new Codelocks('0000000000000000000000000000000000000000', '--');
    }

    /**
     * @covers Codelocks::__construct
     */
    public function testInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);
        $this->assertInstanceOf('\drinkynet\Codelocks\Codelocks', $codelocks);
    }

    /**
     * @covers Codelocks::netcode
     */
    public function testNetcodeInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $netcode = $codelocks->netcode();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\Netcode', $netcode);
    }

    public function testInitInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $init = $codelocks->init();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\Init', $init);
    }

    public function testK3connectInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $lock = $codelocks->k3connect();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\K3connect', $lock);
    }

    public function testLockInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $lock = $codelocks->lock();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\Lock', $lock);
    }

    public function testDurationInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $lock = $codelocks->duration();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\Duration', $lock);
    }

    public function testDurationsInstantiation()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $lock = $codelocks->durations();

        $this->assertInstanceOf('\drinkynet\Codelocks\Methods\Durations', $lock);
    }
}
