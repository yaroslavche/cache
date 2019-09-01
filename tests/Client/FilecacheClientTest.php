<?php

namespace Beryllium\Cache\Tests\Client;

use Beryllium\Cache\Client\FilecacheClient;
use Beryllium\Cache\Statistics\Manager\FilecacheStatisticsManager;
use Beryllium\Cache\Statistics\Tracker\FilecacheStatisticsTracker;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class FilecacheClientTest extends TestCase
{

    /** @var vfsStreamDirectory $vfs*/
    public $vfs;

    /** @var FilecacheClient $cache*/
    public $cache;

    public function setUp(): void
    {
        $this->vfs   = vfsStream::setup('cacheDir');
        $this->cache = new FilecacheClient(vfsStream::url('cacheDir'));
    }

    public function testInstance()
    {
        $this->assertInstanceOf(FilecacheClient::class, $this->cache);
    }

    public function testSetAndGet()
    {
        $this->cache->set('test', 'testing', 20);

        $this->assertEquals('testing', $this->cache->get('test'));
    }

    public function testDelete()
    {
        $this->assertTrue($this->cache->set('test', 'testing', 20));

        $this->assertTrue($this->cache->delete('test'));

        $this->assertSame(null, $this->cache->get('test'));
    }

    public function testStats()
    {
        $stats   = new FilecacheStatisticsTracker(vfsStream::url('cacheDir'));
        $manager = new FilecacheStatisticsManager(vfsStream::url('cacheDir'));

        $this->cache->setStatisticsTracker($stats);
        $this->cache->get('test');
        $this->cache->set('test', 'testing', 300);
        $this->cache->get('test');

        $data = $manager->getStatistics();

        $this->assertEquals(array('File cache'), array_keys($data));

        $numbers = $data['File cache']->getFormattedArray();

        $this->assertEquals(
            array(
                'Hits'        => 1,
                'Misses'      => 1,
                'Helpfulness' => '50.00'
            ),
            $numbers
        );
    }

    public function testClient()
    {
        $this->assertFalse($this->cache->has('unknown_key_' . mt_rand(0, PHP_INT_MAX)));
        $this->assertTrue($this->cache->set('Some_key', 'Some_value', 20));
        $this->assertTrue($this->cache->setMultiple(['k1' => 'v1', 'k2' => 'v2'], 20));
        $this->assertSame('Some_value', $this->cache->get('Some_key'));
        $this->assertEquals(['k1' => 'v1', 'k2' => 'v2'], $this->cache->getMultiple(['k1', 'k2']));
        $this->assertTrue($this->cache->delete('k1'));
        $this->assertFalse($this->cache->has('k1'));
        $this->assertTrue($this->cache->deleteMultiple(['k2']));
        $this->assertFalse($this->cache->has('k2'));
    }

    public function testInvalidPath()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('invalid_path_exception');
        $this->expectExceptionCode(0);
        new FilecacheClient('');
    }
}
