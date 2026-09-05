<?php

namespace Xgrz\PayNow\Observers;

use Xgrz\PayNow\Models\PayNowAttempt;

class PayNowAttemptObserver
{
    public function deleting(PayNowAttempt $payNowAttempt)
    {
        if (config('paynow.protect', true)) {
            throw new \RuntimeException('Cannot delete PayNowAttempt model', $payNowAttempt->toArray());
        }
    }
}
