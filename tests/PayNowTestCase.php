<?php

namespace Xgrz\PayNow\Tests;

use Orchestra\Testbench\TestCase;
use Xgrz\PayNow\PayNowServiceProvider;

abstract class PayNowTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PayNowServiceProvider::class,
        ];
    }

    public function setupPublicCredentials(): void
    {
        config([
            'paynow.credentials.api_key' => '97a55694-5478-43b5-b406-fb49ebfdd2b5',
            'paynow.credentials.signature_key' => 'b305b996-bca5-4404-a0b7-2ccea3d2b64b',
        ]);

    }

}

