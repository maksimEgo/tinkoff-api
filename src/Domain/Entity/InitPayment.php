<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

class InitPayment extends Payment
{
    private string $terminalKey;

    public function getTerminalKey(): string
    {
        return $this->terminalKey;
    }

    private int $amount;

    public function getAmount(): int
    {
        return $this->amount;
    }

    private string $orderId;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    private bool $success;

    public function getSuccess(): bool
    {
        return $this->success;
    }

    private string $paymentId;

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    private ?string $paymentURL;

    public function getPaymentURL(): ?string
    {
        return $this->paymentURL;
    }

    public function __construct(
        string  $terminalKey,
        int     $amount,
        string  $orderId,
        string  $status,
        string  $errorCode,
        bool    $success,
        string  $paymentId,
        ?string $paymentURL   = null,
        ?string $errorMessage = null,
        ?string $errorDetails = null
    ) {
        $this->terminalKey = $terminalKey;
        $this->amount      = $amount;
        $this->orderId     = $orderId;
        $this->status      = $status;
        $this->errorCode   = $errorCode;
        $this->paymentId   = $paymentId;
        $this->success     = $success;

        if (!is_null($paymentURL)) {
            $this->paymentURL = $paymentURL;
        }

        if (!is_null($errorMessage)) {
            $this->errorMessage = $errorMessage;
        }

        if (!is_null($errorDetails)) {
            $this->errorDetails = $errorDetails;
        }
    }
}
