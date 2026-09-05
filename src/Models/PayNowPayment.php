<?php

namespace Xgrz\PayNow\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Xgrz\PayNow\Casts\Amount;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Observers\PayNowPaymentObserver;

#[ObservedBy([PayNowPaymentObserver::class])]
class PayNowPayment extends Model
{
    protected $table = 'paynow_payments';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => Amount::class,
    ];

    protected $with = [
        'attempts',
    ];

    protected $appends = [
        'status',
        'payment_id',
    ];

    public function attempts(): HasMany
    {
        return $this->hasMany(
            PayNowAttempt::class,
            'paynow_payment_id',
        )->latest();
    }

    public function attempt(): HasOne
    {
        return $this->hasOne(
            PayNowAttempt::class,
            'paynow_payment_id',
        )->latestOfMany();
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(
            PayNowRefund::class,
            'paynow_payment_id',
        );
    }

    public function paynowable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStatusAttribute()
    {
        return $this->attempts->first()?->status;
    }

    public function getPaymentIdAttribute(): ?string
    {
        return $this->attempts->first()?->payment_id;
    }

    public function getRefundedAttribute(): null|float
    {
        $amount = $this->refunds
            ->filter(fn(PayNowRefund $refund) => $refund->status !== RefundStatus::FAILED)
            ->sum(fn(PayNowRefund $refund) => $refund->amount);
        return $amount ? : NULL;
    }
}
