<?php

namespace Xgrz\PayNow\Services;

use Paynow\Client;
use Paynow\Environment;

class ConfigService
{
    public static function getApiClient(): Client
    {
        return new Client(
            config('paynow.credentials.api_key'),
            self::getSignatureKey(),
            self::getEnvironment(),
        );
    }

    public static function getSignatureKey(): string
    {
        return config('paynow.credentials.signature_key');
    }

    public static function getEnvironment(): string
    {
        return app()->environment('production')
            ? Environment::PRODUCTION
            : Environment::SANDBOX;
    }
}
