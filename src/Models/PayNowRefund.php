<?php

namespace Xgrz\PayNow\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xgrz\PayNow\Casts\Amount;
use Xgrz\PayNow\Enums\RefundReason;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Observers\PayNowRefundObserver;

#[ObservedBy([PayNowRefundObserver::class])]
class PayNowRefund extends Model
{
    use SoftDeletes;

    protected $table = 'paynow_refunds';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => Amount::class,
        'status' => RefundStatus::class,
        'reason' => RefundReason::class,
    ];

    public function paynowPayment(): BelongsTo
    {
        return $this->belongsTo(PayNowPayment::class, 'paynow_payment_id');
    }

    public function paynowAttempt(): BelongsTo
    {
        return $this->belongsTo(PayNowAttempt::class, 'paynow_attempt_id');
    }
}
