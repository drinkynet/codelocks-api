<?php
/**
 * Tests for the API netcode method
 */

namespace drinkynet\Codelocks\Tests;

use \drinkynet\Codelocks\Codelocks;

class NetcodeTest extends \PHPUnit_Framework_TestCase
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

    public function testGetNetcodeFromAPI()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');
        $lock = getenv('CODELOCKS_TEST_LOCK_ID');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        if (!$lock) {
            $this->markTestSkipped('No CODELOCKS_TEST_LOCK_ID in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        // Get a netcode valid now for the specified lock
        $code = $codelocks->netcode($lock)->get();

        // Netcodes should be a string 6 characters in length
        $this->assertEquals(strlen($code), 6);
    }
}
