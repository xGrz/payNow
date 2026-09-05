<?php

namespace Xgrz\PayNow\Tests;

use Orchestra\Testbench\TestCase;
use Xgrz\PayNow\PayNowServiceProvider;

abstract class PayNowTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    protected function getPackageProviders($app): array
    {
        return [
            PayNowServiceProvider::class,
        ];
    }

}

