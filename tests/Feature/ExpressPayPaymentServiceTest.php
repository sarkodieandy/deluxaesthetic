<?php

namespace Tests\Feature;

use App\DTOs\PaymentInitiationData;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\ExpressPayPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ExpressPayPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payments.expresspay.merchant_id' => 'test-merchant',
            'payments.expresspay.api_key' => 'test-secret-api-key',
            'payments.expresspay.environment' => 'sandbox',
            'payments.expresspay.callback_url' => 'https://store.example/checkout/callback',
            'payments.expresspay.post_url' => 'https://store.example/api/webhooks/expresspay',
        ]);

        Http::preventStrayRequests();
    }

    public function test_initialization_sends_major_units_and_customer_details_and_returns_only_checkout_fields(): void
    {
        $token = 'token-with/characters?and=values';
        Http::fake([
            'https://sandbox.expresspaygh.com/api/submit.php' => Http::response([
                'status' => 1,
                'order-id' => 'PAY-TEST123',
                'token' => $token,
                'api-key' => 'test-secret-api-key',
                'email' => 'customer@example.com',
                'authorization_url' => 'https://untrusted.example/checkout',
            ]),
        ]);

        $result = $this->service()->initialize($this->initiation());

        $this->assertSame([
            'authorization_url' => 'https://sandbox.expresspaygh.com/payment?token='.rawurlencode($token),
            'reference' => 'PAY-TEST123',
            'token' => $token,
        ], $result);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->hasHeader('Content-Type', 'application/x-www-form-urlencoded')
            && $request->data() === [
                'merchant-id' => 'test-merchant',
                'api-key' => 'test-secret-api-key',
                'amount' => '325.09',
                'currency' => 'GHS',
                'order-id' => 'PAY-TEST123',
                'order-desc' => 'ORD-TEST123',
                'firstname' => 'Ama',
                'lastname' => 'Boateng Mensah',
                'phonenumber' => '+233501234567',
                'email' => 'customer@example.com',
                'username' => 'customer@example.com',
                'accountnumber' => '42',
                'redirect-url' => 'https://store.example/checkout/callback',
                'post-url' => 'https://store.example/api/webhooks/expresspay',
            ]);
        Http::assertSentCount(1);
        $this->assertTrue($this->service()->supportsWebhooks());
    }

    public function test_production_uses_official_host_and_guest_email_account_with_callback_fallback(): void
    {
        config([
            'payments.expresspay.environment' => 'production',
            'payments.expresspay.callback_url' => null,
        ]);
        Http::fake([
            'https://expresspaygh.com/api/submit.php' => Http::response([
                'status' => '1', 'order-id' => 'PAY-GUEST', 'token' => 'production-token',
            ]),
        ]);

        $result = $this->service()->initialize(new PaymentInitiationData(
            email: 'guest@example.com',
            amountMinor: 100,
            currency: 'GHS',
            reference: 'PAY-GUEST',
            callbackUrl: 'https://store.example/return',
            metadata: ['name' => 'Guest Buyer', 'phone' => '+233501234567'],
        ));

        $this->assertSame('https://expresspaygh.com/payment?token=production-token', $result['authorization_url']);
        Http::assertSent(fn (Request $request) => $request['amount'] === '1.00'
            && $request['accountnumber'] === 'guest@example.com'
            && $request['redirect-url'] === 'https://store.example/return');
    }

    public function test_production_rejects_insecure_callback_urls_before_sending_credentials(): void
    {
        config([
            'payments.expresspay.environment' => 'production',
            'payments.expresspay.callback_url' => 'http://store.example/checkout/callback',
        ]);
        Http::fake();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('expressPay callback URLs are not configured correctly.');

        try {
            $this->service()->initialize($this->initiation());
        } finally {
            Http::assertNothingSent();
        }
    }

    #[DataProvider('invalidInitializationResponses')]
    public function test_initialization_rejects_invalid_provider_responses(array $response): void
    {
        Http::fake(['https://sandbox.expresspaygh.com/api/submit.php' => Http::response($response)]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to initialise expressPay payment.');

        $this->service()->initialize($this->initiation());
    }

    public static function invalidInitializationResponses(): array
    {
        $response = ['status' => 1, 'order-id' => 'PAY-TEST123', 'token' => 'provider-token'];

        return [
            'invalid credentials' => [array_replace($response, ['status' => 2])],
            'boolean success' => [array_replace($response, ['status' => true])],
            'different reference' => [array_replace($response, ['order-id' => 'PAY-OTHER'])],
            'missing reference' => [array_diff_key($response, ['order-id' => true])],
            'missing token' => [array_diff_key($response, ['token' => true])],
            'blank token' => [array_replace($response, ['token' => ' '])],
            'numeric token' => [array_replace($response, ['token' => 123])],
            'oversize token' => [array_replace($response, ['token' => str_repeat('a', 1025)])],
        ];
    }

    #[DataProvider('missingConfiguration')]
    public function test_missing_or_invalid_configuration_never_falls_back_to_mock(string $key, mixed $value): void
    {
        config(['payments.mock' => true, 'payments.expresspay.'.$key => $value]);

        try {
            $this->service()->initialize($this->initiation());
            $this->fail('Invalid configuration must prevent checkout.');
        } catch (RuntimeException $exception) {
            $this->assertStringNotContainsString('test-secret-api-key', $exception->getMessage());
        }

        Http::assertNothingSent();
    }

    public static function missingConfiguration(): array
    {
        return [
            'no merchant ID' => ['merchant_id', null],
            'no API key' => ['api_key', ''],
            'blank API key' => ['api_key', '   '],
            'unknown environment' => ['environment', 'https://untrusted.example'],
        ];
    }

    public function test_failed_initialization_is_not_retried_or_exposed_in_exception(): void
    {
        Http::fake(['https://sandbox.expresspaygh.com/api/submit.php' => Http::response('test-secret-api-key', 503)]);

        try {
            $this->service()->initialize($this->initiation());
            $this->fail('An HTTP error must prevent checkout.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Unable to initialise expressPay payment.', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }

        Http::assertSentCount(1);
    }

    public function test_connection_failure_does_not_expose_the_underlying_exception(): void
    {
        Http::fake(fn () => throw new ConnectionException('Request contains test-secret-api-key'));

        try {
            $this->service()->initialize($this->initiation());
            $this->fail('Connection failure must prevent checkout.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Unable to initialise expressPay payment.', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }
    }

    #[DataProvider('approvedResponses')]
    public function test_verification_uses_stored_token_and_normalizes_approved_amounts(string $transactionKey, mixed $amount, int $expectedMinor): void
    {
        $this->payment();
        $response = $this->queryResponse();
        unset($response['transaction-id']);
        $response[$transactionKey] = 'EXP-TRANSACTION-123';
        $response['amount'] = $amount;
        $response['email'] = 'private@example.com';
        $response['api-key'] = 'test-secret-api-key';
        $response['result-text'] = 'Echoed sensitive information';
        Http::fake(['https://sandbox.expresspaygh.com/api/query.php' => Http::response($response)]);

        $result = $this->service()->verify('PAY-TEST123');

        $this->assertTrue($result->successful);
        $this->assertSame('successful', $result->status);
        $this->assertSame('EXP-TRANSACTION-123', $result->providerReference);
        $this->assertSame([
            'result' => 1,
            'reference' => 'PAY-TEST123',
            'amount' => $expectedMinor,
            'currency' => 'GHS',
            'transaction_id' => 'EXP-TRANSACTION-123',
        ], $result->raw);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->hasHeader('Content-Type', 'application/x-www-form-urlencoded')
            && $request->data() === [
                'merchant-id' => 'test-merchant',
                'api-key' => 'test-secret-api-key',
                'token' => 'stored-provider-token',
            ]);
    }

    public static function approvedResponses(): array
    {
        return [
            'hyphenated ID and decimal string' => ['transaction-id', '325.09', 32509],
            'underscored ID and integer' => ['transaction_id', 325, 32500],
            'fractional numeric amount' => ['transaction-id', 25.99, 2599],
            'one decimal place' => ['transaction-id', '25.9', 2590],
        ];
    }

    #[DataProvider('unsettledResults')]
    public function test_pending_and_declined_payments_are_never_approved(int $resultCode, string $status): void
    {
        $this->payment();
        $response = array_replace($this->queryResponse(), ['result' => $resultCode]);
        unset($response['transaction-id']);
        Http::fake(['https://sandbox.expresspaygh.com/api/query.php' => Http::response($response)]);

        $result = $this->service()->verify('PAY-TEST123');

        $this->assertFalse($result->successful);
        $this->assertSame($status, $result->status);
        $this->assertNull($result->providerReference);
    }

    public static function unsettledResults(): array
    {
        return ['pending' => [4, 'pending'], 'declined' => [2, 'failed']];
    }

    #[DataProvider('invalidVerificationFields')]
    public function test_verification_rejects_untrusted_or_malformed_responses(string $field, mixed $value): void
    {
        $this->payment();
        Http::fake(['https://sandbox.expresspaygh.com/api/query.php' => Http::response(
            array_replace($this->queryResponse(), [$field => $value]),
        )]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to verify expressPay payment.');

        $this->service()->verify('PAY-TEST123');
    }

    public static function invalidVerificationFields(): array
    {
        return [
            'provider error' => ['result', 3],
            'unknown result' => ['result', 5],
            'boolean result' => ['result', true],
            'different order' => ['order-id', 'PAY-OTHER'],
            'missing order' => ['order-id', null],
            'different token' => ['token', 'other-provider-token'],
            'missing token' => ['token', null],
            'missing amount' => ['amount', null],
            'negative amount' => ['amount', '-1.00'],
            'zero amount' => ['amount', '0.00'],
            'fractional minor unit' => ['amount', '1.001'],
            'scientific notation' => ['amount', '1e2'],
            'non numeric amount' => ['amount', '325.00abc'],
            'boolean amount' => ['amount', true],
            'overflowing amount' => ['amount', '9223372036854775808.00'],
            'missing currency' => ['currency', null],
            'invalid currency' => ['currency', 'GHS extra'],
            'missing transaction ID' => ['transaction-id', null],
            'empty transaction ID' => ['transaction-id', ''],
        ];
    }

    public function test_payment_without_a_stored_token_is_rejected_without_querying_the_provider(): void
    {
        $this->payment()->update(['metadata' => []]);

        try {
            $this->service()->verify('PAY-TEST123');
            $this->fail('A stored expressPay token is required.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Unable to verify expressPay payment.', $exception->getMessage());
        }

        Http::assertNothingSent();
    }

    public function test_payments_for_other_gateways_cannot_be_queried_with_expresspay(): void
    {
        $this->payment()->update(['gateway' => 'paystack']);

        try {
            $this->service()->verify('PAY-TEST123');
            $this->fail('A payment must belong to expressPay.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Unable to verify expressPay payment.', $exception->getMessage());
        }

        Http::assertNothingSent();
    }

    private function service(): ExpressPayPaymentService
    {
        return new ExpressPayPaymentService;
    }

    private function initiation(): PaymentInitiationData
    {
        return new PaymentInitiationData(
            email: 'customer@example.com',
            amountMinor: 32509,
            currency: 'GHS',
            reference: 'PAY-TEST123',
            callbackUrl: 'https://store.example/return',
            metadata: [
                'name' => 'Ama Boateng Mensah',
                'phone' => '+233501234567',
                'customer_id' => 42,
                'order_number' => 'ORD-TEST123',
            ],
        );
    }

    private function payment(): Payment
    {
        return Payment::query()->create([
            'reference' => 'PAY-TEST123',
            'user_id' => User::factory()->create()->id,
            'amount' => '325.09',
            'currency' => 'GHS',
            'gateway' => 'expresspay',
            'status' => 'initiated',
            'metadata' => ['expresspay_token' => 'stored-provider-token'],
        ]);
    }

    private function queryResponse(): array
    {
        return [
            'result' => 1,
            'order-id' => 'PAY-TEST123',
            'token' => 'stored-provider-token',
            'amount' => '325.09',
            'currency' => 'GHS',
            'transaction-id' => 'EXP-TRANSACTION-123',
        ];
    }
}
