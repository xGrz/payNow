<?php

namespace Xgrz\PayNow\Services;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Paynow\Exception\PaynowException;
use Paynow\Service\Payment;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Models\PayNowPayment;

class PayNowPaymentService
{
    public static function payment($orderNumber, $email, int|float $amount, string $currencyCode = 'PLN', ?string $description = NULL, ?string $continueUrl = NULL): ?PayNowPayment
    {
        $paymentExists = PayNowPayment::where('external_id', $orderNumber)->first();
        if ($paymentExists) return $paymentExists;

        $payment = new PayNowPayment;
        $payment->fill([
            'amount' => $amount,
            'currency_code' => $currencyCode,
            'external_id' => $orderNumber,
            'description' => $description ?? $orderNumber,
            'email' => $email,
            'continue_url' => $continueUrl,
            'idempotencyKey' => uniqid($orderNumber . '_'),
        ]);

        return self::sendPayment($payment);
    }

    private static function sendPayment(PayNowPayment $payment): ?PayNowPayment
    {
        try {
            $result = new Payment(ConfigService::getApiClient())
                ->authorize(self::getPaymentPayload($payment));

            $payment->save();
            $payment->attempts()->create([
                'status' => PaymentStatus::findByName($result->getStatus()),
                'payment_id' => $result->getPaymentId(),
            ]);
            $payment->update([
                'link' => $result->getRedirectUrl(),
            ]);

            return $payment;
        } catch (PaynowException $e) {
            Log::error(
                $e->getMessage(),
                [
                    'code' => $e->getCode(),
                    'payload' => self::getPaymentPayload($payment),
                ]
            );
            return NULL;
        }
    }

    private static function getPaymentPayload(PayNowPayment $payment): array
    {
        return [
            'amount' => (int)round($payment->amount * 100),
            'currency' => $payment->currency_code,
            'externalId' => $payment->external_id,
            'description' => $payment->description,
            'buyer' => [
                'email' => $payment->email,
            ],
            'validityTime' => CarbonInterval::fromString(config('paynow.timeout', '24h'))->totalSeconds,
            'continueUrl' => $payment->continue_url,
        ];
    }

}
