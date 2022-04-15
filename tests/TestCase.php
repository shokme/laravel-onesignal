<?php

namespace Shokme\OneSignal\Tests;

use Shokme\OneSignal\OneSignalServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            OneSignalServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }
}
