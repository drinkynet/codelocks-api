<?php
/**
 * Test the Duration method
 */

namespace drinkynet\Codelocks\Tests;

use PHPUnit\Framework\TestCase;
use drinkynet\Codelocks\Codelocks;

class DurationTest extends TestCase
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

    public function testDuration()
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

        $result = $codelocks->duration()
            ->durationId(0)
            ->lockModel($LockModel)
            ->get();

        //
        // Check that the API returned a result
        $this->assertTrue(is_array($result));

        //
        // Check that the result has the expected form
        $this->assertArrayHasKey('LockModel', $result);
        $this->assertArrayHasKey('DurationID', $result);
        $this->assertArrayHasKey('Rules', $result);
            $this->assertArrayHasKey('ApplyDST', $result['Rules']);
            $this->assertArrayHasKey('ConvertToUTC', $result['Rules']);
            $this->assertArrayHasKey('RestrictedStartTime', $result['Rules']);

        $this->assertArrayHasKey('Duration', $result);
            $this->assertArrayHasKey('Days', $result['Duration']);
            $this->assertArrayHasKey('Hours', $result['Duration']);

        $this->assertArrayHasKey('Mode', $result);
            $this->assertArrayHasKey('Name', $result['Mode']);
            $this->assertArrayHasKey('SubMode', $result['Mode']);

        //
        // Check that the returned data matches the request
        $this->assertEquals($result['DurationID'], $durationId);
        $this->assertEquals($result['LockModel'], $LockModel);
    }
}
