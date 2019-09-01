<?php

namespace Beryllium\Cache\Tests\Client;

use Beryllium\Cache\Client\ApcuClient;
use Beryllium\Cache\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ApcuClientTest extends TestCase
{
    /** @var ApcuClient $client */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new ApcuClient();
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ApcuClient::class, $this->client);
    }

    public function testGet()
    {
        $key = 'test-get';
        $this->client->set($key, 'Working');
        $this->assertSame('Working', $this->client->get($key), 'APCu did not contain expected value for key ' . $key);
    }

    public function testDelete()
    {
        $key = 'test-delete';
        $this->client->set($key, 'Working');
        $this->assertTrue($this->client->has($key), 'APCu did not contain expected value for key ' . $key);
        $this->assertTrue($this->client->delete($key));
        $this->assertFalse($this->client->has($key), 'APCu contain unexpected value for key ' . $key);
    }

    public function testClear()
    {
        $key = 'test-clear';
        $this->client->set($key, 'Working');
        $this->assertTrue($this->client->has($key), 'APCu did not contain expected value for key ' . $key);
        $this->assertTrue($this->client->clear());
        $this->assertFalse($this->client->has($key), 'APCu contain unexpected value for key ' . $key);
    }


    public function testGetMultiple()
    {
        $this->client->set('key1', 'value1');
        $this->client->set('key2', 'value3');
        $this->client->set('key3', 'value3');
        $this->assertEquals(['key1' => 'value1', 'key3' => 'value3'], $this->client->getMultiple(['key1', 'key3']));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to getMultiple using non-array/non-Traversable keys');
        $this->expectExceptionCode(0);
        $this->client->getMultiple('key');
    }

    public function testSetMultiple()
    {
        $this->assertTrue($this->client->setMultiple(['key1' => 'value1', 'key3' => 'value3']));
        $this->assertEquals(['key1' => 'value1', 'key3' => 'value3'], $this->client->getMultiple(['key1', 'key3']));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to setMultiple using non-array/non-Traversable values');
        $this->expectExceptionCode(0);
        $this->client->setMultiple('key');
    }

    public function testDeleteMultiple()
    {
        $this->assertTrue($this->client->setMultiple(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']));
        $this->assertTrue($this->client->deleteMultiple(['key1', 'key3']));
        $this->assertFalse($this->client->has('key1'));
        $this->assertTrue($this->client->has('key2'));
        $this->assertFalse($this->client->has('key3'));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to deleteMultiple using non-array/non-Traversable keys');
        $this->expectExceptionCode(0);
        $this->client->deleteMultiple('key');
    }

    public function testHas()
    {
        $this->client->clear();
        $this->assertFalse($this->client->has('key1'));
        $this->client->set('key1', 'value1');
        $this->assertTrue($this->client->has('key1'));
        $this->assertFalse($this->client->has('key2'));
    }

}
