<?php

namespace DataFabric\SDK\Tests;

use DataFabric\SDK\KycClient;

class KycClientTest extends BaseTestCase
{
    private string $testApiKey = 'dfb_test_example_key';

    public function testClientInstantiation(): void
    {
        $client = new KycClient($this->testApiKey);

        $this->assertInstanceOf(KycClient::class, $client);
        $this->assertTrue($client->isTestMode());
    }

    public function testClientWithLiveKey(): void
    {
        $client = new KycClient('dfb_live_example_key');

        $this->assertFalse($client->isTestMode());
    }

    public function testCustomBaseUrl(): void
    {
        $customUrl = 'http://localhost:8000';
        $client = new KycClient($this->testApiKey, $customUrl);

        $this->assertEquals($customUrl, $client->getBaseUrl());
    }

    public function testDefaultBaseUrl(): void
    {
        $client = new KycClient($this->testApiKey);

        $this->assertEquals('https://datafabric.hiroshiaki.com', $client->getBaseUrl());
    }
}
