<?php

namespace Xgrz\PayNow\Observers;

use Xgrz\PayNow\Models\PayNowRefund;

class PayNowRefundObserver
{
    public function deleting(PayNowRefund $payNowRefund): void
    {
        if (config('paynow.protect', true) && filled($payNowRefund->refund_id)) {
            throw new \RuntimeException('Cannot delete PayNowRefund model', $payNowRefund->toArray());
        }
    }
}
