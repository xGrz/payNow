<?php

namespace Xgrz\PayNow\Tests\Transaction;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Xgrz\PayNow\Enums\PaymentStatus;
use Xgrz\PayNow\Models\PaymentTransaction;
use Xgrz\PayNow\Models\PayNowPayment;
use Xgrz\PayNow\Tests\PayNowTestCase;

class TransactionTest extends PayNowTestCase
{

    use DatabaseMigrations;

    public function test_transaction_minimal_setup(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10);
        $payload = $transaction->payload();

        $this->assertArrayHasKey('amount', $payload);
        $this->assertEquals(10010, $payload['amount']);

        $this->assertArrayHasKey('currency', $payload);
        $this->assertEquals('PLN', $payload['currency']);

        $this->assertArrayHasKey('externalId', $payload);
        $this->assertEquals('Order ZZ/2020/2021', $payload['externalId']);

        $this->assertArrayHasKey('description', $payload);
        $this->assertEquals('Order ZZ/2020/2021', $payload['description']);

        $this->assertArrayHasKey('buyer', $payload);
        $this->assertArrayHasKey('email', $payload['buyer']);
        $this->assertEquals('test@example.com', $payload['buyer']['email']);

        $this->assertArrayHasKey('validityTime', $payload);
        $this->assertEquals(43200, $payload['validityTime']);

        $this->assertCount(6, collect($payload)->dot(), 'To meny fields in payload');
    }

    public function test_can_assign_callback_url(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->callbackUrl('/callback/test/route');

        $this->assertArrayHasKey('continueUrl', $transaction->payload());
        $this->assertEquals('/callback/test/route', $transaction->payload()['continueUrl']);
    }

    public function test_can_assign_description(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->description('Order ZZ/2020/2021a');

        $payload = $transaction->payload();

        $this->assertArrayHasKey('description', $payload);
        $this->assertEquals('Order ZZ/2020/2021a', $payload['description']);
    }

    public function test_can_modify_validity_time(): void
    {
        config(['paynow.timeout' => '60m']);
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10);

        $payload = $transaction->payload();

        $this->assertArrayHasKey('validityTime', $payload);
        $this->assertEquals(3600, $payload['validityTime']);
    }

    public function test_can_assign_customer_name_in_one_string(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->name('John Doe');

        $payload = collect($transaction->payload())->dot();

        $this->assertArrayHasKey('buyer.firstName', $payload);
        $this->assertArrayHasKey('buyer.lastName', $payload);
        $this->assertEquals('John', $payload['buyer.firstName']);
        $this->assertEquals('Doe', $payload['buyer.lastName']);
    }

    public function test_can_assign_customer_name_separated_first_and_last_name(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->name('John', 'Doe');

        $payload = collect($transaction->payload())->dot();

        $this->assertArrayHasKey('buyer.firstName', $payload);
        $this->assertArrayHasKey('buyer.lastName', $payload);
        $this->assertEquals('John', $payload['buyer.firstName']);
        $this->assertEquals('Doe', $payload['buyer.lastName']);
    }

    public function test_can_assign_phone_number(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->phone('123-456-789');

        $payload = collect($transaction->payload())->dot();

        $this->assertArrayHasKey('buyer.phone.prefix', $payload);
        $this->assertArrayHasKey('buyer.phone.number', $payload);
        $this->assertEquals('+48', $payload['buyer.phone.prefix']);
        $this->assertEquals('123456789', $payload['buyer.phone.number']);
    }

    public function test_can_assign_valid_billing_address(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->billingAddress('al. Jerozolimskie', '100', '22', '00-950', 'Warszawa');

        $payload = collect($transaction->payload())->dot();

        $this->assertArrayHasKey('buyer.address.billing.street', $payload);
        $this->assertEquals('al. Jerozolimskie', $payload['buyer.address.billing.street']);
        $this->assertEquals('100', $payload['buyer.address.billing.houseNumber']);
        $this->assertEquals('22', $payload['buyer.address.billing.apartmentNumber']);
        $this->assertEquals('00-950', $payload['buyer.address.billing.zipCode']);
        $this->assertEquals('Warszawa', $payload['buyer.address.billing.city']);

        $this->assertArrayNotHasKey('buyer.address.shipping.street', $payload);
    }

    public function test_can_assign_valid_billing_address_without_apartment_number(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->billingAddress('al. Jerozolimskie', '100', NULL, '00-950', 'Warszawa');

        $payload = collect($transaction->payload())->dot();
        $this->assertArrayHasKey('buyer.address.billing.street', $payload);
        $this->assertArrayNotHasKey('buyer.address.billing.apartmentNumber', $payload);
    }

    public function test_can_assign_valid_shipping_address(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->shippingAddress('al. Jerozolimskie', '100', '22', '00-950', 'Warszawa');

        $payload = collect($transaction->payload())->dot();

        $this->assertArrayHasKey('buyer.address.shipping.street', $payload);
        $this->assertEquals('al. Jerozolimskie', $payload['buyer.address.shipping.street']);
        $this->assertEquals('100', $payload['buyer.address.shipping.houseNumber']);
        $this->assertEquals('22', $payload['buyer.address.shipping.apartmentNumber']);
        $this->assertEquals('00-950', $payload['buyer.address.shipping.zipCode']);
        $this->assertEquals('Warszawa', $payload['buyer.address.shipping.city']);

        $this->assertArrayNotHasKey('buyer.address.billing.street', $payload);
    }

    public function test_can_assign_valid_shipping_address_without_apartment_number(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->shippingAddress('al. Jerozolimskie', '100', NULL, '00-950', 'Warszawa');

        $payload = collect($transaction->payload())->dot();
        $this->assertArrayHasKey('buyer.address.shipping.street', $payload);
        $this->assertArrayNotHasKey('buyer.address.shipping.apartmentNumber', $payload);
    }

    public function test_can_assign_selected_payment_method(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10)
            ->shippingAddress('al. Jerozolimskie', '100', NULL, '00-950', 'Warszawa')
            ->method(202);

        $payload = collect($transaction->payload())->dot();
        $this->assertArrayHasKey('paymentMethodId', $payload);
        $this->assertEquals(202, $payload['paymentMethodId']);
    }

    public function test_payment_method_not_assigned_is_not_placed_in_payload(): void
    {
        $transaction = PaymentTransaction::make('test@example.com', 'Order ZZ/2020/2021', 100.10);

        $payload = collect($transaction->payload())->dot();
        $this->assertArrayNotHasKey('paymentMethodId', $payload);
    }

    public function test_can_send_payment()
    {

        config([
            'paynow.credentials.api_key' => '97a55694-5478-43b5-b406-fb49ebfdd2b5',
            'paynow.credentials.signature_key' => 'b305b996-bca5-4404-a0b7-2ccea3d2b64b',
        ]);
        $purposeOfPayment = 'Order ZZ/2020/2021-' . time();

        $transaction = PaymentTransaction::make('test@example.com', $purposeOfPayment, 100.10)
            ->callbackUrl('https://google.com')
            ->send()
            ->refresh();

        $this->assertDatabaseHas((new PayNowPayment)->getTable(), [
            'id' => $transaction->id,
            'external_id' => $purposeOfPayment,
            'amount' => 10010,
            'currency_code' => 'PLN',
            'description' => $purposeOfPayment,
            'email' => 'test@example.com',
        ]);

        $this->assertSame(PaymentStatus::NEW, $transaction->status);
        $this->assertSame(PaymentStatus::NEW, $transaction->attempt->status);
        $this->assertNotEmpty($transaction->attempt->payment_id, 'Transaction payment id (from api) is empty');
    }

}