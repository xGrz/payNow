<?php

namespace Xgrz\PayNow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Xgrz\PayNow\Events\PayNowPaymentStatusChangedEvent;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Models\PayNowAttempt;
use Xgrz\PayNow\Models\PayNowPayment;

class NotificationWebhookController
{
    public function __invoke(Request $request)
    {
        Log::info('PayNow notification received', $request->json('paymentId'));

        $consumed = PayNow::handleNotification($request);
        if ($consumed) {

            $paymentId = $request->json('paymentId');
            $payment = PayNowAttempt::query()
                ->where('payment_id', $paymentId)
                ->with('payment.paynowable')
                ->first()
                ->payment;

            if ($payment instanceof PayNowPayment) {
                PayNowPaymentStatusChangedEvent::dispatch($payment);
            }
        }

        return $consumed
            ? response('Accepted', 202)
            : response('Bad Request', 400);
    }
}
