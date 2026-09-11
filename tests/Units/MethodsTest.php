<?php

namespace Xgrz\PayNow\Tests\Units;

use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Tests\PayNowTestCase;

class MethodsTest extends PayNowTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupPublicCredentials();
    }


    public function test_can_get_methods(): void
    {
        $methods = PayNow::methods(1000);
        $this->assertNotEmpty($methods);
    }

    public function test_can_get_available_methods(): void
    {
        $allMethods = PayNow::methods(1000);
        $available = PayNow::availableMethods(1000);

        $this->assertNotEmpty($available);
        $this->assertGreaterThan($available, $allMethods);
    }

    public function test_methods_has_all_required_props(): void
    {
        $methods = PayNow::availableMethods(1000);
        $index = array_key_first($methods);

        $method = $methods[$index];

        $this->assertIsInt($index);
        $this->assertIsArray($method);
        $this->assertArrayHasKey('id', $method);
        $this->assertArrayHasKey('type', $method);
        $this->assertArrayHasKey('name', $method);
        $this->assertArrayHasKey('available', $method);
        $this->assertArrayHasKey('description', $method);
        $this->assertArrayHasKey('image', $method);
    }
}