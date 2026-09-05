<?php

namespace Xgrz\PayNow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Events\PayNowPaymentStatusChangedEvent;
use Xgrz\PayNow\Facades\PayNow;
use Xgrz\PayNow\Models\PayNowAttempt;

class UpdatePayNowAttemptsStatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?PayNowAttempt $attempt = NULL)
    {
    }

    public function handle(): void
    {
        $this->attempt
            ? $this->updateAttemptStatus($this->attempt)
            : $this->updateAll();
    }

    private function updateAll(): void
    {
        PayNowAttempt::query()
            ->whereIn('status', [PaymentStatus::NEW, PaymentStatus::PENDING, PaymentStatus::ERROR])
            ->where('updated_at', '>', now()->subDays(3))
            ->get()
            ->each(fn(PayNowAttempt $attempt) => $this->updateAttemptStatus($attempt));
    }

    private function updateAttemptStatus(?PayNowAttempt $attempt = NULL): void
    {
        if (! $attempt) return;

        try {
            $attemptStatus = PayNow::paymentStatus($attempt->payment_id);
            if ($attemptStatus instanceof PaymentStatus) {
                $previousStatus = $attempt->status;
                $attempt->update(['status' => $attemptStatus]);
                if ($attemptStatus != $previousStatus) {
                    PayNowPaymentStatusChangedEvent::dispatch($attempt->payment);
                }

            }

        } catch (\Throwable $e) {
            Log::warning('PayNow payment status update failed: ' . $e->getMessage());
        }
    }
}
