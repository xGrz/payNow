<?php

namespace Xgrz\PayNow\Services;

use Illuminate\Support\Facades\Log;
use Paynow\Exception\PaynowException;
use Paynow\Service\Refund;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Enums\RefundReason;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Models\PayNowPayment;
use Xgrz\PayNow\Models\PayNowRefund;

class PayNowRefundService
{
    public static function refund(PayNowPayment $payment, int|float $amount, ?RefundReason $reason = NULL)
    {
        if ($payment->status !== PaymentStatus::CONFIRMED) return NULL;
        $refund = PayNowRefund::create([
            'paynow_payment_id' => $payment->id,
            'paynow_attempt_id' => $payment->attempt->id,
            'amount' => $amount,
            'reason' => $reason,
        ]);

        try {
            $refundAction = new Refund(ConfigService::getApiClient())
                ->create(
                    $payment->attempt->payment_id,
                    uniqid($payment->attempt->id . '_'),
                    (int)round($amount * 100),
                    $reason->name,
                );

            $refund->update([
                'status' => $refundAction->getStatus(),
                'refund_id' => $refundAction->getRefundId(),
            ]);
        } catch (PaynowException $e) {
            $refund->update([
                'status' => RefundStatus::FAILED,
                'error' => collect($e->getErrors())->first()?->getMessage(),
            ]);
            Log::error(
                $e->getMessage(),
                [
                    'api_error' => collect($e->getErrors())->first()?->getMessage(),
                    'refund_id' => $refund->id,
                    'user_id' => auth()->id(),
                ]
            );
        }
        return $refund;
    }

    public static function reasons(): array
    {
        return collect(RefundReason::cases())
            ->mapWithKeys(fn(RefundReason $case) => [$case->name => $case->getLabel()])
            ->toArray();
    }

}
