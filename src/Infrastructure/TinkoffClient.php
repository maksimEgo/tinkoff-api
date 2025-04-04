<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Domain\Token\FinishAuthorizeTokenGenerator;
use Egorov\TinkoffApi\Domain\Token\GetStateTokenGenerator;
use Egorov\TinkoffApi\Domain\Token\InitTokenGenerator;
use Egorov\TinkoffApi\Infrastructure\Mapper\AuthorizedPaymentMapper;
use Egorov\TinkoffApi\Infrastructure\Mapper\InitPaymentMapper;
use Egorov\TinkoffApi\Infrastructure\Mapper\StatePaymentMapper;
use Exception;
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

        $tokenGenerator = new InitTokenGenerator($this->password);
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

        $tokenGenerator = new GetStateTokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('GetState', $data);

        return StatePaymentMapper::fromArray($response);
    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $endpoint, array $data): array
    {
        try {
            $options = [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $data
            ];

            $response = $this->httpClient->post($endpoint, $options);
            $body = $response->getBody()->getContents();

            $responseData = json_decode($body, true);

            if (!$responseData) {
                throw new \RuntimeException("Invalid JSON response from Tinkoff API");
            }

            if (isset($responseData['Success']) && $responseData['Success'] === false) {
                $errorMsg     = $responseData['Message'] ?? 'Unknown error';
                $errorCode    = $responseData['ErrorCode'] ?? 'unknown';
                $errorDetails = $responseData['Details'] ?? '';

                throw new \RuntimeException(
                    "Tinkoff API error: {$errorMsg}, code: {$errorCode}, details: {$errorDetails}"
                );
            }

            return $responseData;
        } catch (GuzzleException $e) {
            error_log("HTTP error: " . $e->getMessage());
            throw new \RuntimeException("HTTP request failed: " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            error_log("General error: " . $e->getMessage());
            throw $e;
        }
    }

    public function sendClosingReceipt(string $paymentId, Receipt $receipt): bool
    {
        $paymentStatus = $this->getPaymentStatus($paymentId);

        if ($paymentStatus->getStatus() !== 'CONFIRMED') {
            throw new \RuntimeException(
                "Закрывающий чек можно отправить только для платежей в статусе CONFIRMED. " .
                "Текущий статус: " . $paymentStatus->getStatus()
            );
        }

        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId' => $paymentId,
            'Receipt' => $receipt->toArray()
        ];

        $tokenGenerator = new InitTokenGenerator($this->password);
        $data['Token'] = $tokenGenerator->generate($data);

        $response = $this->sendRequest('SendClosingReceipt', $data);

        return $response['Success'] === true;
    }

    /*
     * in development
     */
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

        $tokenGenerator = new FinishAuthorizeTokenGenerator($this->password);
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