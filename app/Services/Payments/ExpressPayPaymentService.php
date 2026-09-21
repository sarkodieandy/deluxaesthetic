<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\PaymentInitiationData;
use App\DTOs\PaymentVerificationResult;
use App\Exceptions\Payments\PaymentVerificationMismatchException;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExpressPayPaymentService implements PaymentGatewayInterface
{
    public function initialize(PaymentInitiationData $data): array
    {
        [$baseUrl, $credentials] = $this->configuration();
        [$firstName, $lastName] = array_pad(preg_split('/\s+/', trim($data->metadata['name'] ?? ''), 2), 2, '');

        if ($data->amountMinor <= 0
            || mb_strlen($data->email) > 64
            || mb_strlen($data->reference) > 64
            || ! preg_match('/^[A-Z]{3}$/D', $data->currency)) {
            throw new RuntimeException('Unable to initialise expressPay payment.');
        }

        $redirectUrl = (string) (config('payments.expresspay.callback_url') ?: $data->callbackUrl);
        $postUrl = (string) (config('payments.expresspay.post_url') ?: route('api.webhooks.expresspay'));
        $this->validateCallbackUrl($redirectUrl, $baseUrl);
        $this->validateCallbackUrl($postUrl, $baseUrl);

        $response = $this->post($baseUrl.'/api/submit.php', $credentials + [
            'amount' => intdiv($data->amountMinor, 100).'.'.str_pad((string) ($data->amountMinor % 100), 2, '0', STR_PAD_LEFT),
            'currency' => $data->currency,
            'order-id' => $data->reference,
            'order-desc' => $data->metadata['order_number'] ?? $data->reference,
            'firstname' => mb_substr($firstName, 0, 32),
            'lastname' => mb_substr($lastName, 0, 64),
            'phonenumber' => $data->metadata['phone'] ?? '',
            'email' => $data->email,
            'username' => $data->email,
            'accountnumber' => (string) ($data->metadata['customer_id'] ?? $data->email),
            'redirect-url' => $redirectUrl,
            'post-url' => $postUrl,
        ], 'Unable to initialise expressPay payment.');

        if (! in_array($response['status'] ?? null, [1, '1'], true)
            || ! is_string($response['order-id'] ?? null)
            || ! hash_equals($data->reference, $response['order-id'])
            || ! $this->validToken($response['token'] ?? null)) {
            throw new RuntimeException('Unable to initialise expressPay payment.');
        }

        return [
            'authorization_url' => $baseUrl.'/payment?token='.rawurlencode($response['token']),
            'reference' => $data->reference,
            'token' => $response['token'],
        ];
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        [$baseUrl, $credentials] = $this->configuration();
        $payment = Payment::query()->where('reference', $reference)->where('gateway', 'expresspay')->first();
        $token = $payment?->metadata['expresspay_token'] ?? null;

        if (! $this->validToken($token)) {
            throw new RuntimeException('Unable to verify expressPay payment.');
        }

        $response = $this->post($baseUrl.'/api/query.php', $credentials + ['token' => $token], 'Unable to verify expressPay payment.');
        $result = $response['result'] ?? null;
        $currency = $response['currency'] ?? null;
        $amountMinor = $this->amountMinor($response['amount'] ?? null);
        $transactionId = $response['transaction-id'] ?? $response['transaction_id'] ?? null;

        if (! in_array($result, [1, '1', 2, '2', 4, '4'], true)
            || ! is_string($response['order-id'] ?? null)
            || ! is_string($response['token'] ?? null)
            || $amountMinor === null
            || ! is_string($currency)
            || ! preg_match('/^[A-Z]{3}$/D', $currency)) {
            throw new RuntimeException('Unable to verify expressPay payment.');
        }

        if (! hash_equals($reference, $response['order-id']) || ! hash_equals($token, $response['token'])) {
            throw new PaymentVerificationMismatchException('Unable to verify expressPay payment.');
        }

        if (is_int($transactionId)) {
            $transactionId = (string) $transactionId;
        }

        $validTransactionId = is_string($transactionId) && trim($transactionId) !== '' && strlen($transactionId) <= 64;

        if ((int) $result === 1 && ! $validTransactionId) {
            throw new RuntimeException('Unable to verify expressPay payment.');
        }

        $transactionId = $validTransactionId ? $transactionId : null;

        return new PaymentVerificationResult(
            successful: (int) $result === 1,
            reference: $reference,
            status: match ((int) $result) {
                1 => 'successful',
                2 => 'failed',
                4 => 'pending',
            },
            providerReference: $transactionId,
            raw: [
                'result' => (int) $result,
                'reference' => $reference,
                'amount' => $amountMinor,
                'currency' => $currency,
                'transaction_id' => $transactionId,
            ],
        );
    }

    public function supportsWebhooks(): bool
    {
        return true;
    }

    private function configuration(): array
    {
        $merchantId = config('payments.expresspay.merchant_id');
        $apiKey = config('payments.expresspay.api_key');

        if (! is_string($merchantId) || trim($merchantId) === '' || mb_strlen(trim($merchantId)) > 256
            || ! is_string($apiKey) || trim($apiKey) === '' || mb_strlen(trim($apiKey)) > 256) {
            throw new RuntimeException('expressPay merchant credentials are not configured.');
        }

        $baseUrl = match (config('payments.expresspay.environment', 'sandbox')) {
            'sandbox' => 'https://sandbox.expresspaygh.com',
            'production' => 'https://expresspaygh.com',
            default => throw new RuntimeException('expressPay environment must be sandbox or production.'),
        };

        return [$baseUrl, ['merchant-id' => trim($merchantId), 'api-key' => trim($apiKey)]];
    }

    private function post(string $url, array $payload, string $errorMessage): array
    {
        try {
            $response = Http::asForm()->acceptJson()->withoutRedirecting()->connectTimeout(5)->timeout(20)->post($url, $payload);
        } catch (ConnectionException) {
            // Do not attach the HTTP exception: it can contain request credentials.
            throw new RuntimeException($errorMessage);
        }

        $body = $response->json();

        if (! $response->successful() || ! is_array($body)) {
            throw new RuntimeException($errorMessage);
        }

        return $body;
    }

    private function validToken(mixed $token): bool
    {
        return is_string($token) && trim($token) !== '' && strlen($token) <= 1024;
    }

    private function validateCallbackUrl(string $url, string $baseUrl): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        if (mb_strlen($url) > 256 || ! is_string($host) || $host === '' || ! in_array($scheme, ['http', 'https'], true)
            || ($baseUrl === 'https://expresspaygh.com' && $scheme !== 'https')) {
            throw new RuntimeException('expressPay callback URLs are not configured correctly.');
        }
    }

    private function amountMinor(mixed $amount): ?int
    {
        if ((! is_int($amount) && ! is_float($amount) && ! is_string($amount))
            || ! preg_match('/^\d+(?:\.\d{1,2})?$/D', (string) $amount)) {
            return null;
        }

        [$major, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');
        $minor = ltrim($major.str_pad($fraction, 2, '0'), '0');
        $maximum = (string) PHP_INT_MAX;

        if ($minor === '' || strlen($minor) > strlen($maximum)
            || (strlen($minor) === strlen($maximum) && strcmp($minor, $maximum) > 0)) {
            return null;
        }

        return (int) $minor;
    }
}
