<?php

namespace Xgrz\PayNow\Services;

use Illuminate\Support\Facades\Log;
use Paynow\Exception\ConfigurationException;
use Paynow\Exception\PaynowException;
use Paynow\Model\PaymentMethods\PaymentMethod;
use Paynow\Service\Payment;
use Xgrz\PayNow\Exceptions\PayNowException as GrzPayNowException;

class PayNowMethodsService
{
    public static function all(float $amount, string $currencyCode = 'PLN'): array
    {
        try {
            $methods = (new Payment(ConfigService::getApiClient()))
                ->getPaymentMethods($currencyCode, $amount)
                ->getAll();
            return collect($methods)
                ->transform(fn(PaymentMethod $method) => [
                    'id' => $method->getId(),
                    'type' => $method->getType(),
                    'name' => $method->getName(),
                    'available' => $method->isEnabled(),
                    'description' => $method->getDescription(),
                    'image' => $method->getImage(),
                ])
                ->keyBy('id')
                ->toArray();
        } catch (ConfigurationException $e) {
            $logMessage = $e->getMessage();
            Log::error($logMessage, [
                'amount' => $amount,
                'currencyCode' => $currencyCode,
            ]);
            throw $e;
        } catch (PaynowException $e) {
            $logMessage = collect($e->getErrors())->first()->getMessage();
            Log::error($logMessage, [
                'amount' => $amount,
                'currencyCode' => $currencyCode,
            ]);
            throw new GrzPayNowException($logMessage);
        } finally {
            return [];
        }
    }

    public static function available(float $amount, string $currencyCode = 'PLN'): array
    {
        return collect(self::all($amount, $currencyCode))
            ->filter(fn(array $method) => $method['available'])
            ->toArray();
    }

    public static function options(float $amount, string $currencyCode = 'PLN'): array
    {
        return collect(self::available($amount, $currencyCode))
            ->filter(fn(array $method) => $method['available'])
            ->map(fn(array $method) => $method['name'])
            ->toArray();
    }

}
