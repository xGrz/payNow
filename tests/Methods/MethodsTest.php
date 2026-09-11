<?php

namespace Xgrz\PayNow\Tests\Methods;

use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Tests\PayNowTestCase;

class MethodsTest extends PayNowTestCase
{
    public function test_can_get_methods()
    {
        $this->setupPublicCredentials();
        $methods = PayNow::methods(1000);
        $this->assertNotEmpty($methods);
    }
}