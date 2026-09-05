<?php

namespace Xgrz\PayNow\Facades;

use Illuminate\Http\Request;
use Paynow\Exception\ConfigurationException;
use Paynow\Exception\PaynowException;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Exceptions\PayNowStatusNameException;
use Xgrz\PayNow\Models\PaymentTransaction;
use Xgrz\PayNow\Models\PayNowPayment;
use Xgrz\PayNow\Services\PayNowMethodsService;
use Xgrz\PayNow\Services\PayNowNotificationService;
use Xgrz\PayNow\Services\PayNowRefundService;
use Xgrz\PayNow\Services\PayNowStatusService;

class PayNow
{
    public static function methods(float $amount, string $currencyCode = 'PLN'): array
    {
        return PayNowMethodsService::all($amount, $currencyCode);
    }

    public static function availableMethods(float $amount, string $currencyCode = 'PLN'): array
    {
        return PayNowMethodsService::available($amount, $currencyCode);
    }

    public static function payment()
    {
        // todo
    }


    public static function refundReasons(): array
    {
        return PayNowRefundService::reasons();
    }

    /**
     * @throws PaynowException
     * @throws ConfigurationException
     * @throws PayNowStatusNameException
     */
    public static function paymentStatus(string $paymentId): ?PaymentStatus
    {
        return PayNowStatusService::payment($paymentId);
    }

    /**
     * @throws PaynowException
     * @throws ConfigurationException
     * @throws PayNowStatusNameException
     */
    public static function refundStatus(string $refundId): ?RefundStatus
    {
        return PayNowStatusService::refund($refundId);
    }

    public static function handleNotification(Request $request): bool
    {
        return PayNowNotificationService::make($request);
    }

    /**
     * @throws PaynowException
     */
    public static function send(PaymentTransaction $transaction): PayNowPayment
    {
        return $transaction->send();
    }
}
