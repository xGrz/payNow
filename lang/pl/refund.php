<?php

use Xgrz\PayNow\Enums\RefundReason;
use Xgrz\PayNow\Enums\RefundStatus;

return [
    'status' => [
        RefundStatus::NEW->name => 'Utworzona',
        RefundStatus::PENDING->name => 'Oczekuje',
        RefundStatus::SUCCESSFUL->name => 'Zakończona',
        RefundStatus::FAILED->name => 'Nieudana',
    ],
    'description' => [
        RefundStatus::NEW->name => 'Zwrot został utworzony',
        RefundStatus::PENDING->name => 'Zwrot oczekuje na realizację',
        RefundStatus::SUCCESSFUL->name => 'Dokonaliśmy zwrotu',
        RefundStatus::FAILED->name => 'Zwrot nieudany'
    ],
    'reason' => [
        RefundReason::OTHER->name => 'Inny powód',
        RefundReason::RMA->name => 'Uznanie reklamacji',
        RefundReason::REFUND_BEFORE_14->name => 'Zwrot ustawowy (14 dni)',
        RefundReason::REFUND_AFTER_14->name => 'Zwrot po 14 dniu',
    ],

];
