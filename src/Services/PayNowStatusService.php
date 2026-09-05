<?php

namespace Xgrz\PayNow\Services;

use Paynow\Exception\ConfigurationException;
use Paynow\Exception\PaynowException;
use Paynow\Service\Payment;
use Paynow\Service\Refund;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Exceptions\PayNowStatusNameException;

class PayNowStatusService
{
    /**
     * @throws PaynowException
     * @throws ConfigurationException
     * @throws PayNowStatusNameException
     */
    public static function payment(string $paymentId):?PaymentStatus
    {
        $payment = new Payment(ConfigService::getApiClient());
        return PaymentStatus::findByName($payment->status($paymentId)->getStatus());
    }

    /**
     * @throws PaynowException
     * @throws ConfigurationException
     * @throws PayNowStatusNameException
     */
    public static function refund(string $refundId): ?RefundStatus
    {
        $refund = new Refund(ConfigService::getApiClient());
        return RefundStatus::findByName($refund->status($refundId)->getStatus());
    }

}
