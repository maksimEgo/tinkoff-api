<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Domain\Exception\HttpException;
use Egorov\TinkoffApi\Domain\Exception\InvalidResponseException;
use Egorov\TinkoffApi\Domain\Exception\TinkoffApiException;
use Egorov\TinkoffApi\Domain\Token\TokenGenerator;
use Egorov\TinkoffApi\Infrastructure\Mapper\AuthorizedPaymentMapper;
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
        ]);
    }

    public function initiatePayment(Order $order): Payment
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'Amount'      => $order->getAmount()->getValue(),
            'OrderId'     => $order->getOrderId()->getValue()
        ];

        if ($order->getDescription()) {
            $data['Description'] = $order->getDescription();
        }

        if ($order->getCustomerKey()) {
            $data['CustomerKey'] = $order->getCustomerKey();
        }

        if ($order->getRecurrent()) {
            $data['Recurrent'] = $order->getRecurrent();
        }

        if ($order->getPayType()) {
            $data['PayType'] = $order->getPayType()->value;
        }

        if ($order->getLanguage()) {
            $data['Language'] = $order->getLanguage()->value;
        }

        if ($order->getNotificationURL()) {
            $data['NotificationURL'] = $order->getNotificationURL();
        }

        if ($order->getSuccessURL()) {
            $data['SuccessURL'] = $order->getSuccessURL();
        }

        if ($order->getFailURL()) {
            $data['FailURL'] = $order->getFailURL();
        }

        if ($order->getRedirectDueDate()) {
            $data['RedirectDueDate'] = $order->getRedirectDueDate()->format('Y-m-d\TH:i:sP');
        }

        if ($order->getReceipt()) {
            $data['Receipt'] = $order->getReceipt()->toArray();
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('Init', $data);

        return InitPaymentMapper::fromArray($response);
    }

    public function getPaymentStatus(string $paymentId, ?string $clientIp = null): Payment
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
        ];

        if ($clientIp !== null) {
            $data['IP'] = $clientIp;
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('GetState', $data);

        return StatePaymentMapper::fromArray($response);
    }

    private function sendRequest(string $endpoint, array $data): array
    {
        try {
            $options = [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $data
            ];

            $response = $this->httpClient->post($endpoint, $options);
            $body = $response->getBody()->getContents();

            return $this->parseResponse($body);
        } catch (GuzzleException $e) {
            throw new HttpException("HTTP request failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function sendAbsoluteRequest(string $url, array $data): array
    {
        try {
            $client = new Client();
            $options = [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $data
            ];

            $response = $client->post($url, $options);
            $body = $response->getBody()->getContents();

            return $this->parseResponse($body);
        } catch (GuzzleException $e) {
            throw new HttpException("HTTP request failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function parseResponse(string $body): array
    {
        $responseData = json_decode($body, true);

        if (!$responseData) {
            throw new InvalidResponseException("Invalid JSON response from Tinkoff API");
        }

        if (isset($responseData['Success']) && $responseData['Success'] === false) {
            $errorMsg     = $responseData['Message'] ?? 'Unknown error';
            $errorCode    = $responseData['ErrorCode'] ?? 'unknown';
            $errorDetails = $responseData['Details'] ?? '';

            throw new TinkoffApiException($errorMsg, $errorCode, $errorDetails);
        }

        return $responseData;
    }

    public function sendClosingReceipt(string $paymentId, Receipt $receipt): bool
    {
        $paymentStatus = $this->getPaymentStatus($paymentId);

        if ($paymentStatus->getStatus() !== 'CONFIRMED') {
            throw new TinkoffApiException(
                "Закрывающий чек можно отправить только для платежей в статусе CONFIRMED. " .
                "Текущий статус: " . $paymentStatus->getStatus(),
                '0',
                ''
            );
        }

        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId' => $paymentId,
            'Receipt' => $receipt->toArray()
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $cashboxUrl = str_replace('/v2/', '/cashbox/', $this->baseUrl);
        $response = $this->sendAbsoluteRequest($cashboxUrl . 'SendClosingReceipt', $data);

        return $response['Success'] === true;
    }

    public function confirmPayment(string $paymentId, ?int $amount = null, ?Receipt $receipt = null): Payment
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
        ];

        if ($amount !== null) {
            $data['Amount'] = $amount;
        }

        if ($receipt !== null) {
            $data['Receipt'] = $receipt->toArray();
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('Confirm', $data);

        return StatePaymentMapper::fromArray($response);
    }

    public function cancelPayment(string $paymentId, ?int $amount = null, ?Receipt $receipt = null): Payment
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
        ];

        if ($amount !== null) {
            $data['Amount'] = $amount;
        }

        if ($receipt !== null) {
            $data['Receipt'] = $receipt->toArray();
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('Cancel', $data);

        return StatePaymentMapper::fromArray($response);
    }

    public function chargePayment(string $paymentId, string $rebillId): Payment
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
            'RebillId'    => $rebillId,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('Charge', $data);

        return StatePaymentMapper::fromArray($response);
    }

    public function checkOrder(string $orderId): array
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'OrderId'     => $orderId,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('CheckOrder', $data);

        $payments = [];
        if (isset($response['Payments'])) {
            foreach ($response['Payments'] as $paymentData) {
                $payments[] = StatePaymentMapper::fromArray(array_merge(
                    $paymentData,
                    ['TerminalKey' => $response['TerminalKey'], 'OrderId' => $response['OrderId']]
                ));
            }
        }

        return $payments;
    }

    public function addCustomer(string $customerKey, ?string $email = null, ?string $phone = null): array
    {
        $data = [
            'TerminalKey'  => $this->terminalKey,
            'CustomerKey'  => $customerKey,
        ];

        if ($email !== null) {
            $data['Email'] = $email;
        }

        if ($phone !== null) {
            $data['Phone'] = $phone;
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('AddCustomer', $data);
    }

    public function getCustomer(string $customerKey): array
    {
        $data = [
            'TerminalKey'  => $this->terminalKey,
            'CustomerKey'  => $customerKey,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('GetCustomer', $data);
    }

    public function removeCustomer(string $customerKey): array
    {
        $data = [
            'TerminalKey'  => $this->terminalKey,
            'CustomerKey'  => $customerKey,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('RemoveCustomer', $data);
    }

    public function getCardList(string $customerKey): array
    {
        $data = [
            'TerminalKey'  => $this->terminalKey,
            'CustomerKey'  => $customerKey,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('GetCardList', $data);
    }

    public function removeCard(string $customerKey, string $cardId): array
    {
        $data = [
            'TerminalKey'  => $this->terminalKey,
            'CustomerKey'  => $customerKey,
            'CardId'       => $cardId,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('RemoveCard', $data);
    }

    public function resend(): array
    {
        $data = [
            'TerminalKey' => $this->terminalKey,
        ];

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        return $this->sendRequest('Resend', $data);
    }

    public function finishAuthorize(
        string $paymentId,
        array $cardData,
        ?string $ip = null,
        ?array $additionalParams = null
    ): Payment {
        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId'   => $paymentId,
            'CardData'    => $this->formatCardData($cardData)
        ];

        if ($ip !== null) {
            $data['IP'] = $ip;
        }

        if ($additionalParams !== null) {
            foreach ($additionalParams as $key => $value) {
                $data[$key] = $value;
            }
        }

        $tokenGenerator = new TokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('FinishAuthorize', $data);

        return AuthorizedPaymentMapper::fromArray($response);
    }

    private function formatCardData(array $cardData): string
    {
        if (isset($cardData['encryptedData'])) {
            return $cardData['encryptedData'];
        }

        $formattedData = '';

        if (isset($cardData['PAN'])) {
            $formattedData .= 'PAN=' . $cardData['PAN'] . ';';
        }

        if (isset($cardData['ExpDate'])) {
            $formattedData .= 'ExpDate=' . $cardData['ExpDate'] . ';';
        }

        if (isset($cardData['CardHolder'])) {
            $formattedData .= 'CardHolder=' . $cardData['CardHolder'] . ';';
        }

        if (isset($cardData['CVV'])) {
            $formattedData .= 'CVV=' . $cardData['CVV'] . ';';
        }

        if (isset($cardData['ECI'])) {
            $formattedData .= 'ECI=' . $cardData['ECI'] . ';';
        }

        if (isset($cardData['CAVV'])) {
            $formattedData .= 'CAVV=' . $cardData['CAVV'] . ';';
        }

        $formattedData = rtrim($formattedData, ';');

        return $formattedData;
    }
}