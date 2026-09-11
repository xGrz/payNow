<?php

namespace Xgrz\PayNow\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Models\PaymentTransaction;
use Xgrz\PayNow\Tests\PayNowTestCase;

class CreateTransactionTest extends PayNowTestCase
{

    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupPublicCredentials();
    }

    private function setupPaymentTransaction(): PaymentTransaction
    {
        return PayNow::buildPayment('test@example.com', 'Order ' . microtime(true), 200)
            ->callbackUrl('https://google.com/');
    }

    public function test_transaction_has_external_ident_assigned()
    {
        $payment = self::setupPaymentTransaction()->send();

        $paymentIdent = $payment->attempt->payment_id;

        $this->assertNotNull($paymentIdent);
        $this->assertGreaterThan(15, str($paymentIdent)->length());
    }

    public function test_transaction_has_status_set_to_new()
    {
        $payment = self::setupPaymentTransaction()->send();

        $this->assertSame(PaymentStatus::NEW, $payment->status);
        $this->assertSame(PaymentStatus::NEW, $payment->attempt->status);
    }

    public function test_can_get_payment_status_from_api(): void
    {
        $payment = self::setupPaymentTransaction()->send();
        $originalStatus = $payment->status;

        $status = PayNow::paymentStatus($payment->attempt->payment_id);

        $this->assertInstanceOf(PaymentStatus::class, $status);
        $this->assertSame($originalStatus, $status);
    }
}