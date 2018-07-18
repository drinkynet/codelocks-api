<?php
/**
 * Test the lock method
 *
 * This is a clone of the K3connectTest class because the Lock method
 * class extends it for backward compatability reasons
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

    public function testGetLock()
    {
        $key = getenv('CODELOCKS_API_KEY');
        $accessKey = getenv('CODELOCKS_API_ACCESS_KEY');
        $lockId = getenv('CODELOCKS_TEST_LOCK_ID');

        if (!$key) {
            $this->markTestSkipped('No CODELOCKS_API_KEY in ENV');
        }

        if (!$accessKey) {
            $this->markTestSkipped('No CODELOCKS_API_ACCESS_KEY in ENV');
        }

        if (!$lockId) {
            $this->markTestSkipped('No CODELOCKS_TEST_LOCK_ID in ENV');
        }

        $codelocks = new Codelocks($key, $accessKey);

        $lock = $codelocks->lock()->lockId($lockId)->get();

        $this->assertTrue(is_array($lock));
        $this->assertEquals($lock['LockId'], $lockId);

        // Verify that the lock array is in the expected form
        $this->assertArrayHasKey('LockId', $lock);
        $this->assertArrayHasKey('LockName', $lock);
        $this->assertArrayHasKey('BatteryStatus', $lock);
        $this->assertArrayHasKey('BatteryUpdated', $lock);
        $this->assertArrayHasKey('Manufacturer', $lock);
        $this->assertArrayHasKey('ModelName', $lock);
        $this->assertArrayHasKey('FirmwareVersion', $lock);
        $this->assertArrayHasKey('Timezone', $lock);
        $this->assertArrayHasKey('TimezoneOffset', $lock);
        $this->assertArrayHasKey('DstStart', $lock);
        $this->assertArrayHasKey('DstEnd', $lock);
        $this->assertArrayHasKey('DstTime', $lock);
        $this->assertArrayHasKey('PairingDate', $lock);
        $this->assertArrayHasKey('LockLocation', $lock);

        // Additional variables present in the return but not the spec
        $this->assertArrayHasKey('LockDescription', $lock);
        $this->assertArrayHasKey('LockSecondaryId', $lock);
        $this->assertArrayHasKey('LockTimecode', $lock);
        $this->assertArrayHasKey('LockSmsId', $lock);
        $this->assertArrayHasKey('UtcBased', $lock);
        $this->assertArrayHasKey('Optional', $lock);
        $this->assertArrayHasKey('Programs', $lock);
        $this->assertArrayHasKey('ModelProvider', $lock);
        $this->assertArrayHasKey('ModelDisplay', $lock);
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

        // If there are no locks returned by the API $locks should be exactly false
        // otherwise the $locks should be an array
        $result = (is_array($locks) || $locks === false ) ? true : false;
        $this->assertTrue($result);

        // If we got an array of locks and contains at least one lock
        // verify that the first one is in the expected form
        if (is_array($locks) && count($locks) > 0) {
            $this->assertArrayHasKey('LockId', $locks[0]);
            $this->assertArrayHasKey('LockName', $locks[0]);
            $this->assertArrayHasKey('BatteryStatus', $locks[0]);
            $this->assertArrayHasKey('BatteryUpdated', $locks[0]);
            $this->assertArrayHasKey('Manufacturer', $locks[0]);
            $this->assertArrayHasKey('ModelName', $locks[0]);
            $this->assertArrayHasKey('FirmwareVersion', $locks[0]);
            $this->assertArrayHasKey('Timezone', $locks[0]);
            $this->assertArrayHasKey('TimezoneOffset', $locks[0]);
            $this->assertArrayHasKey('DstStart', $locks[0]);
            $this->assertArrayHasKey('DstEnd', $locks[0]);
            $this->assertArrayHasKey('DstTime', $locks[0]);
            $this->assertArrayHasKey('PairingDate', $locks[0]);
            $this->assertArrayHasKey('LockLocation', $locks[0]);

            // Additional variables present in the return but not the spec
            $this->assertArrayHasKey('LockDescription', $locks[0]);
            $this->assertArrayHasKey('LockSecondaryId', $locks[0]);
            $this->assertArrayHasKey('LockTimecode', $locks[0]);
            $this->assertArrayHasKey('LockSmsId', $locks[0]);
            $this->assertArrayHasKey('UtcBased', $locks[0]);
            $this->assertArrayHasKey('Optional', $locks[0]);
            $this->assertArrayHasKey('Programs', $locks[0]);
            $this->assertArrayHasKey('ModelProvider', $locks[0]);
            $this->assertArrayHasKey('ModelDisplay', $locks[0]);
        }
    }
}
