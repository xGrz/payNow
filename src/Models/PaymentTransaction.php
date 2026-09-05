<?php

namespace Xgrz\PayNow\Models;

use Carbon\CarbonInterval;

class PaymentTransaction
{
    private string $identifier;
    protected ?int $amount = NULL;
    protected string $currency_code = 'PLN';
    protected ?string $description = NULL;
    protected string $email;
    protected ?string $firstName = NULL;
    protected ?string $lastName = NULL;
    protected ?string $phone = NULL;
    protected array $billing = [
        'zipCode' => '',
        'city' => '',
        'street' => '',
        'houseNumber' => '',
        'apartmentNumber' => '',
        'country' => 'PL',
    ];
    protected array $shipping = [
        'zipCode' => '',
        'city' => '',
        'street' => '',
        'houseNumber' => '',
        'apartmentNumber' => '',
        'country' => 'PL',
    ];
    private ?string $callbackUrl = NULL;
    private ?PayNowPayment $payment = NULL;
    private ?int $payNowMethodId = NULL;

    public static function make(string $identifier): static
    {
        return new static($identifier);
    }

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->payment = new PayNowPayment();
    }

    public function amount(float $amount, string $currencyCode = 'PLN'): static
    {
        $this->amount = (int)round($amount * 100);
        $this->currency_code = $currencyCode;
        return $this;
    }

    public function phone(?string $phone): static
    {
        $this->phone = $phone ? preg_replace('/\D/', '', $phone) : NULL;
        return $this;
    }

    public function email(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function name(string $firstName, ?string $lastName = NULL): static
    {
        if (empty($lastName)) {
            $lastName = explode(' ', $firstName, 2)[1] ?? NULL;
            $firstName = explode(' ', $firstName, 2)[0];
        }
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function callbackUrl(string $url): static
    {
        $this->callbackUrl = $url;
        return $this;
    }

    public function method(int $id): static
    {
        // check if payment method is available

        $this->payNowMethodId = $id;
        return $this;
    }

    public function billingAddress(?string $street, ?string $houseNumber, ?string $apartmentNumber, ?string $zipCode, ?string $city, ?string $country = 'PL'): static
    {
        $this->billing['street'] = $street;
        $this->billing['houseNumber'] = $houseNumber;
        $this->billing['apartmentNumber'] = $apartmentNumber;
        $this->billing['zipCode'] = $zipCode;
        $this->billing['city'] = $city;
        $this->billing['country'] = $country;

        return $this;
    }

    public function shippingAddress(?string $street, ?string $houseNumber, ?string $apartmentNumber, ?string $zipCode, ?string $city, ?string $country = 'PL'): static
    {
        $this->shipping['street'] = $street;
        $this->shipping['houseNumber'] = $houseNumber;
        $this->shipping['apartmentNumber'] = $apartmentNumber;
        $this->shipping['zipCode'] = $zipCode;
        $this->shipping['city'] = $city;
        $this->shipping['country'] = $country;
        return $this;
    }

    public function getPhoneStruct(): ?array
    {
        if (empty($this->phone)) return NULL;
        return [
            'prefix' => '+48',
            'number' => preg_replace('/\D/', '', $this->phone),
        ];
    }

    public function getBillingAddress(): ?array
    {
        $addressTest = collect($this->billing)
            ->filter(fn($value, $key) => $key !== 'apartmentNumber')
            ->filter(fn($value) => ! empty($value))
            ->count();

        return $addressTest === 5
            ? $this->billing
            : NULL;
    }

    public function getShippingAddress(): ?array
    {
        $addressTest = collect($this->shipping)
            ->filter(fn($value, $key) => $key !== 'apartmentNumber')
            ->filter(fn($value) => ! empty($value))
            ->count();

        return $addressTest === 5
            ? $this->shipping
            : NULL;
    }

    public function payload(): array
    {
        $payload = [
            'amount' => $this->amount,
            'currency' => $this->currency_code,
            'externalId' => $this->identifier,
            'description' => $this->description,
            'paymentMethodId' => $this->payNowMethodId,
            'buyer' => [
                'email' => $this->email,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'phone' => $this->getPhoneStruct(),
                'address' => [
                    'billing' => $this->getBillingAddress(),
                    'shipping' => $this->getShippingAddress(),
                ],
            ],
            'validityTime' => (int)CarbonInterval::fromString(config('paynow.timeout', '12h'))->totalSeconds,
            'continueUrl' => $this->callbackUrl,
        ];

        return collect($payload)
            ->dot()
            ->filter(fn($value) => ! is_null($value))
            ->undot()
            ->toArray();
    }

}
