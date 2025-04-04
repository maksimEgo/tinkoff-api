<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

/*
 * in development
 */
class AuthorizedPayment extends Payment
{
    private ?string $terminalKey = null;

    public function setTerminalKey(string $terminalKey): void
    {
        $this->terminalKey = $terminalKey;
    }

    public function getTerminalKey(): ?string
    {
        return $this->terminalKey;
    }

    private ?int $amount = null;

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    private ?string $orderId = null;

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    private bool $success = false;

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    private ?string $paymentId = null;

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    private ?string $rebillId = null;

    public function setRebillId(string $rebillId): void
    {
        $this->rebillId = $rebillId;
    }

    public function getRebillId(): ?string
    {
        return $this->rebillId;
    }

    private ?string $cardId = null;

    public function setCardId(string $cardId): void
    {
        $this->cardId = $cardId;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }
}