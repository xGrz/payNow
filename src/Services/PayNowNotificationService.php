<?php

namespace Xgrz\PayNow\Services;

use Illuminate\Http\Request;
use Paynow\Exception\SignatureVerificationException;
use Paynow\Notification;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Exceptions\PayNowStatusNameException;
use Xgrz\PayNow\Models\PayNowAttempt;
use Xgrz\PayNow\Models\PayNowPayment;

class PayNowNotificationService
{
    /**
     * @throws PayNowStatusNameException
     */
    public static function make(?Request $request = NULL): bool
    {
        if (! $request) {
            $request = request();
        }
        $headers = [
            'signature' => $request->headers->get('signature'),
            'pos-id' => $request->headers->get('pos-id'),
        ];
        try {
            new Notification(ConfigService::getSignatureKey(), $request->getContent(), $headers);
        } catch (SignatureVerificationException $e) {
            return false;
        }

        $transaction = PayNowPayment::with('attempts')
            ->where('external_id', $request->json('externalId'))
            ->first();

        $attempt = $transaction
            ->attempts
            ->filter(fn(PayNowAttempt $attempt) => $attempt->payment_id === $request->json('paymentId'));

        if ($attempt->isEmpty()) {
            $transaction
                ->attempts()
                ->create([
                    'payment_id' => $request->json('paymentId'),
                    'status' => PaymentStatus::findByName($request->json('status')),
                ]);
        } else {
            $attempt
                ->first()
                ->update(['status' => PaymentStatus::findByName($request->json('status'))]);
        }

        return true;
    }

}
