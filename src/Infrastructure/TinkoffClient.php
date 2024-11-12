<?php

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\InitPayment;
use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;
use Egorov\TinkoffApi\Infrastructure\Mapper\InitPaymentMapper;
use Egorov\TinkoffApi\Infrastructure\Mapper\StatePaymentMapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TinkoffClient implements PaymentClientInterface
{
    private string $terminalKey;

    public function getTerminalKey(): string
    {
        return $this->terminalKey;
    }

    private string $password;

    public function getPassword(): string
    {
        return $this->password;
    }

    private Client $httpClient;

    private string $baseUrl;

    public function __construct(
        string $terminalKey,
        string $password,
        string $baseUrl = 'https://securepay.tinkoff.ru/v2/'
    ) {
        $this->terminalKey = $terminalKey;
        $this->password    = $password;
        $this->baseUrl     = $baseUrl;

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'verify'   => false,
        ]);
    }

    public function initiatePayment(Order $order): Payment
    {
        $data = [
            'Amount'          => $order->getAmount()->getValue(),
            'OrderId'         => $order->getOrderId()->getValue(),
            'Description'     => $order->getDescription(),
            'CustomerKey'     => $order->getCustomerKey(),
            'Recurrent'       => $order->getRecurrent(),
            'PayType'         => $order->getPayType()->value ?? null,
            'Language'        => $order->getLanguage()->value ?? null,
            'NotificationURL' => $order->getNotificationURL(),
            'SuccessURL'      => $order->getSuccessURL(),
            'FailURL'         => $order->getFailURL(),
        ];

        $response = $this->sendRequest('Init', $data);

        return InitPaymentMapper::fromArray($response);
    }

    public function getPaymentStatus(string $paymentId, ?string $clientIp = null): Payment
    {
        $data = [
            'PaymentId' => $paymentId,
        ];

        if ($clientIp !== null) {
            $data['IP'] = $clientIp;
        }

        $response = $this->sendRequest('GetState', $data);

        return StatePaymentMapper::fromArray($response);
    }

    private function sendRequest(string $endpoint, array $data): array
    {
        $data['TerminalKey'] = $this->terminalKey;
        $data['Token'] = $this->generateToken($data);

        try {
            $response = $this->httpClient->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            $body = $response->getBody()->getContents();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            error_log('Request to Tinkoff failed: ' . $e->getMessage());
            throw new \RuntimeException('Tinkoff API request failed.');
        }
    }

    private function generateToken(array $data): string
    {
        $data['Password'] = $this->password;
        ksort($data);
        $values = array_values($data);
        return hash('sha256', implode('', $values));
    }
}