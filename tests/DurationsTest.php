<?php
/**
 * Test the durations method
 */

namespace drinkynet\Codelocks\Tests;

use \drinkynet\Codelocks\Codelocks;

class DurationsTest extends \PHPUnit_Framework_TestCase
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

    public function testDurations()
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


        $durationId = 0;
        $LockModel  = 'K3CONNECT';

        $result = $codelocks->durations()
            ->lockModel($LockModel)
            ->get();

        // Check the result is an array
        $this->assertTrue(is_array($result));

        // Check that it has at least one dimension
        $this->assertTrue(count($result) >= 1);

        // Check that the first item has the desired format
        $this->assertArrayHasKey('DurationId', $result[0]);
        $this->assertArrayHasKey('Mode', $result[0]);
        $this->assertArrayHasKey('SubMode', $result[0]);
        $this->assertArrayHasKey('DurationDays', $result[0]);
        $this->assertArrayHasKey('DurationHours', $result[0]);
        $this->assertArrayHasKey('LockModels', $result[0]);
    }
}
