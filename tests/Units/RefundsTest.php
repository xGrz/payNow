<?php

use Xgrz\PayNow\Enums\RefundReason;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Tests\PayNowTestCase;

class RefundsTest extends PayNowTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupPublicCredentials();
    }

    public function test_can_fetch_refund_reasons(): void
    {
        $reasonsCount = collect(RefundReason::cases())->count();

        $reasons = PayNow::refundReasons();
        $this->assertNotEmpty($reasons);
        $this->assertCount($reasonsCount, $reasons);
    }


}