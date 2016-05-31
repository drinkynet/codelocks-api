<?php
/**
 * Test the lock method
 */

namespace drinkynet\Codelocks\Tests;

use \drinkynet\Codelocks\Codelocks;

class LockTest extends \PHPUnit_Framework_TestCase
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

    public function testGetLocks()
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

        $locks = $codelocks->lock()->get();

        // Check if the result is an array of locks or exactly false (no locks
        // found)
        $result = (is_array($locks) || $locks === false ) ? true : false;

        $this->assertTrue($result);
    }
}
