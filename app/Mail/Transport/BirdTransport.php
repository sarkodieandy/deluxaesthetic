<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class BirdTransport extends AbstractTransport
{
    protected string $apiKey;

    protected string $endpoint;

    public function __construct(
        string $apiKey,
        string $endpoint = 'https://us1.platform.bird.com/v1/email/messages',
        ?EventDispatcherInterface $dispatcher = null,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($dispatcher, $logger);

        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();
        if ($original instanceof Message && ! $original instanceof Email) {
            $original = MessageConverter::toEmail($original);
        }

        if (! $original instanceof Email) {
            throw new TransportException('Bird mail transport can only send Symfony Email messages.');
        }

        $from = $this->stringifyAddresses($original->getFrom());
        $to = $this->stringifyAddresses($original->getTo());

        if (empty($from)) {
            throw new TransportException('The Bird mail transport requires a From address.');
        }

        if (empty($to)) {
            throw new TransportException('The Bird mail transport requires at least one To address.');
        }

        $subject = $original->getSubject() ?: '';
        $html = $original->getHtmlBody() ?? $original->getTextBody() ?? '';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
            'from' => $from[0],
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ]);

        if ($response->failed()) {
            throw new TransportException('Bird mail transport failed: '.$response->body());
        }

        if ($id = $response->json('id')) {
            $message->setMessageId($id);
        }
    }

    protected function stringifyAddresses(array $addresses): array
    {
        return array_map(static fn (Address $address) => $address->toString(), $addresses);
    }
}
