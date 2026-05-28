<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

class AuthorizedPayment extends Payment
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

    private ?string $rebillId = null;

    public function getRebillId(): ?string
    {
        return $this->rebillId;
    }

    private ?string $cardId = null;

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    private ?string $acsUrl = null;

    public function getAcsUrl(): ?string
    {
        return $this->acsUrl;
    }

    private ?string $md = null;

    public function getMd(): ?string
    {
        return $this->md;
    }

    private ?string $paReq = null;

    public function getPaReq(): ?string
    {
        return $this->paReq;
    }

    public function __construct(
        string  $terminalKey,
        int     $amount,
        string  $orderId,
        string  $status,
        string  $errorCode,
        bool    $success,
        string  $paymentId,
        ?string $errorMessage = null,
        ?string $errorDetails = null,
        ?string $rebillId = null,
        ?string $cardId = null,
        ?string $acsUrl = null,
        ?string $md = null,
        ?string $paReq = null
    ) {
        $this->terminalKey  = $terminalKey;
        $this->amount       = $amount;
        $this->orderId      = $orderId;
        $this->status       = $status;
        $this->errorCode    = $errorCode;
        $this->success      = $success;
        $this->paymentId    = $paymentId;
        $this->errorMessage = $errorMessage;
        $this->errorDetails = $errorDetails;
        $this->rebillId     = $rebillId;
        $this->cardId       = $cardId;
        $this->acsUrl       = $acsUrl;
        $this->md           = $md;
        $this->paReq        = $paReq;
    }

    public function requires3DS(): bool
    {
        return $this->acsUrl !== null;
    }
}
