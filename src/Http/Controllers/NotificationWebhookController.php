<?php

namespace Xgrz\PayNow\Http\Controllers;

use Illuminate\Http\Request;
use Xgrz\PayNow\Events\PayNowPaymentStatusChangedEvent;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Models\PayNowAttempt;

class NotificationWebhookController
{
    public function __invoke(Request $request)
    {
        $consumed = PayNow::handleNotification($request);
        if ($consumed) {

            $paymentId = $request->json('paymentId');
            $payment = PayNowAttempt::query()
                ->where('payment_id', $paymentId)
                ->with('payment.paynowable')
                ->first()
                ->payment;

            PayNowPaymentStatusChangedEvent::dispatch($payment);
        }

        return $consumed
            ? response('Accepted', 202)
            : response('Bad Request', 400);
    }
}
