<?php

namespace Xgrz\PayNow\Observers;

use Xgrz\PayNow\Models\PayNowPayment;

class PayNowPaymentObserver
{
    public function deleting(PayNowPayment $payNowPayment): void
    {
        if (config('paynow.protect', true)) {
            throw new \RuntimeException('Cannot delete PayNowPayment model', $payNowPayment->toArray());
        }
    }
}
