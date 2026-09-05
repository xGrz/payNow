<?php

namespace Xgrz\PayNow\Enums;

enum RefundReason: string
{
    case OTHER = 'OTHER';
    case RMA = 'RMA';
    case REFUND_BEFORE_14 = 'REFUND_BEFORE_14';
    case REFUND_AFTER_14 = 'REFUND_AFTER_14';

    public function getColor(): string
    {
        return match ($this) {
            self::RMA => 'info',
            self::REFUND_BEFORE_14 => 'warning',
            self::REFUND_AFTER_14 => 'danger',
            self::OTHER => 'gray',
        };
    }

    public function getLabel(): string
    {
        return  __('paynow::refund.reason.' . $this->name);
    }
}
