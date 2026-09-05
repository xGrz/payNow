<?php

use Xgrz\PayNow\Enums\PaymentStatus;

return [
    'status' => [
        PaymentStatus::NEW->name => 'Utworzona',
        PaymentStatus::PENDING->name => 'Rozpoczęta',
        PaymentStatus::CONFIRMED->name => 'Zakończona',
        PaymentStatus::EXPIRED->name => 'Expired',
        PaymentStatus::REJECTED->name => 'Odrzucona',
        PaymentStatus::ERROR->name => 'Błąd',
        PaymentStatus::ABANDONED->name => 'Opuszczona',

    ],
    'description' => [
        PaymentStatus::NEW->name => 'Płatność utworzona - czekamy na wpłatę',
        PaymentStatus::PENDING->name => 'Płatność w toku',
        PaymentStatus::CONFIRMED->name => 'Otrzymaliśmy Twoją zapłatę',
        PaymentStatus::EXPIRED->name => 'Płatność wygasła - ponów próbę zapłaty',
        PaymentStatus::REJECTED->name => 'Bank odrzucił Twoją płatność',
        PaymentStatus::ERROR->name => 'Wystąpił błąd płatności',
        PaymentStatus::ABANDONED->name => 'Płatność nie została zakończona',
    ],
];
