<?php

namespace DataFabric\SDK\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for DataFabric SDK tests
 */
abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
