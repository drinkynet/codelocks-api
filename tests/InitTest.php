<?php
/**
 * Test the init method
 */

namespace drinkynet\Codelocks\Tests;

use \drinkynet\Codelocks\Codelocks;

class InitTest extends \PHPUnit_Framework_TestCase
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

    public function testGetInitSequence()
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

        $init = $codelocks->init()->lockModel('KL1550')->get();

        $this->assertArrayHasKey('DefaultMastercode', $init);
        $this->assertArrayHasKey('InitSeqFormat', $init);
        $this->assertArrayHasKey('InitSeqFormatDesc', $init);
        $this->assertArrayHasKey('InitSeq', $init);
        $this->assertArrayHasKey('TimeCode', $init);
    }

    public function testGetInitSequenceWithMasterCode()
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

        $init = $codelocks->init()->lockModel('KL1550')->masterCode('12345678')->get();

        $this->assertArrayHasKey('DefaultMastercode', $init);
        $this->assertArrayHasKey('InitSeqFormat', $init);
        $this->assertArrayHasKey('InitSeqFormatDesc', $init);
        $this->assertArrayHasKey('InitSeq', $init);
        $this->assertArrayHasKey('TimeCode', $init);
    }
}
