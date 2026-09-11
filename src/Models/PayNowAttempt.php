<?php

namespace Xgrz\PayNow\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use xGrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Observers\PayNowAttemptObserver;

/**
 * @property-read  integer $id
 * @property integer       $paynow_payment_id
 * @property string        $payment_id
 * @property string        $status
 */
#[ObservedBy([PayNowAttemptObserver::class])]
class PayNowAttempt extends Model
{
    protected $table = 'paynow_attempts';

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    protected $guarded = ['id'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            PayNowPayment::class,
            'paynow_payment_id',
            'id',
        );
    }

}
