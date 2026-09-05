<?php

namespace Xgrz\PayNow\Console\Commands;

use Illuminate\Console\Command;
use Xgrz\PayNow\Jobs\UpdatePayNowRefundsStatesJob;

class PayNowRefundsStatesCommand extends Command
{
    protected $signature = 'paynow:refunds-status-update';

    protected $description = 'Update all refunds states';

    public function handle(): int
    {
        new UpdatePayNowRefundsStatesJob()->handle();

        return 0;
    }
}
