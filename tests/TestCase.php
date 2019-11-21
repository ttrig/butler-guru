<?php

namespace Butler\Guru\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    public static $dispatched = [];

    protected function setUp(): void
    {
        self::$dispatched = [];

        require_once __DIR__ . '/dispatch.php';
    }

    protected function assertDispatched(string $className): void
    {
        foreach (self::$dispatched as $dispatched) {
            if ($dispatched instanceof $className) {
                $this->assertTrue(true);
                return;
            }
        }

        $this->fail($className . ' was not dispatched');
    }

    protected function assertNotDispatched(string $className): void
    {
        foreach (self::$dispatched as $dispatched) {
            if ($dispatched instanceof $className) {
                $this->fail($className . ' was dispatched');
            }
        }

        $this->assertTrue(true);
    }
}
