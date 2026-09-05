<?php

namespace Xgrz\PayNow\Enums;

use Xgrz\PayNow\Exceptions\PayNowStatusNameException;

enum PaymentStatus: string
{
    case NEW = 'NEW';
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case EXPIRED = 'EXPIRED';
    case REJECTED = 'REJECTED';
    case ERROR = 'ERROR';
    case ABANDONED = 'ABANDONED';

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::PENDING => 'info',
            self::CONFIRMED => 'success',
            self::EXPIRED, self::ABANDONED => 'warning',
            self::REJECTED, self::ERROR => 'danger',
            default => '',
        };
    }

    public function getLabel(): string
    {
        return __('paynow::payment.status.' . $this->name);
    }

    public function getDescription(): string
    {
        return __('paynow::payment.description.' . $this->name);
    }

    /**
     * @throws PayNowStatusNameException
     */
    public static function findByName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === strtoupper($name)) {
                return $case;
            }
        }
        throw new  PayNowStatusNameException("Unknown payment status name: `$name`");
    }
}
