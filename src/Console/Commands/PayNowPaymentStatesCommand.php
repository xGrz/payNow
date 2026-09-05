<?php

namespace Xgrz\PayNow\Console\Commands;

use Illuminate\Console\Command;
use Xgrz\PayNow\Jobs\UpdatePayNowAttemptsStatesJob;

class PayNowPaymentStatesCommand extends Command
{
    protected $signature = 'paynow:payments-status-update';

    protected $description = 'Update all payments states';

    public function handle(): int
    {
        new UpdatePayNowAttemptsStatesJob()->handle();

        return 0;
    }
}
