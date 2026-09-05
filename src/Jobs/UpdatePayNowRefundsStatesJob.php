<?php

namespace Xgrz\PayNow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Xgrz\PayNow\Enums\RefundStatus;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Models\PayNowRefund;

class UpdatePayNowRefundsStatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?PayNowRefund $refund = NULL)
    {
    }

    public function handle(): void
    {
        $this->refund
            ? $this->updateRefundStatus($this->refund)
            : $this->updateAll();
    }

    public function updateAll(): void
    {
        PayNowRefund::query()
            ->whereIn('status', [RefundStatus::NEW, RefundStatus::PENDING])
            ->get()
            ->each(fn(PayNowRefund $refund) => $this->updateRefundStatus($refund));
    }

    public function updateRefundStatus(?PayNowRefund $refund = NULL): void
    {
        if (! $refund) return;

        try {
            $refundStatus = PayNow::refundStatus($refund->refund_id);
            if ($refundStatus instanceof RefundStatus) {
                $refund->update(['status' => $refundStatus]);
            }
        } catch (\Throwable $e) {
            Log::warning('PayNow refund status update failed: ' . $e->getMessage());
        }
    }
}
