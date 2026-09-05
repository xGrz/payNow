<?php

namespace Xgrz\PayNow\Enums;

use Xgrz\PayNow\Exceptions\PayNowStatusNameException;

enum RefundStatus: string
{
    case NEW = 'NEW';
    case PENDING = 'PENDING';
    case SUCCESSFUL = 'SUCCESSFUL';
    case FAILED = 'FAILED';

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::PENDING => 'info',
            self::SUCCESSFUL => 'success',
            self::FAILED => 'danger',
        };
    }

    public function getLabel(): string
    {
        return __('paynow::refund.status.' . $this->name);
    }

    public function getDescription(): string
    {
        return __('paynow::refund.description.' . $this->name);
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
        throw new  PayNowStatusNameException("Unknown refund status name: `$name`");
    }

}
