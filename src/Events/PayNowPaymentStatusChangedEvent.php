<?php

namespace Xgrz\PayNow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Xgrz\PayNow\Models\PayNowPayment;

class PayNowPaymentStatusChangedEvent
{
    use Dispatchable;

    public function __construct(public PayNowPayment $payment)
    {

    }
}
