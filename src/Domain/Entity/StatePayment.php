<?php

namespace Egorov\TinkoffApi\Domain\Entity;

use Egorov\TinkoffApi\Domain\Enum\RouteEnum;
use Egorov\TinkoffApi\Domain\Enum\SourceEnum;

class StatePayment extends Payment
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

    private ?RouteEnum $route = null;

    public function getRoute(): ?RouteEnum
    {
        return $this->route;
    }

    private ?SourceEnum $source = null;

    public function getSource(): ?SourceEnum
    {
        return $this->source;
    }

    private ?int $creditAmount = null;

    public function getCreditAmount(): ?int
    {
        return $this->creditAmount;
    }

    public function __construct(
        string      $terminalKey,
        int         $amount,
        string      $orderId,
        string      $status,
        string      $errorCode,
        bool        $success,
        string      $paymentId,
        ?string     $errorMessage = null,
        ?string     $errorDetails = null,
        ?RouteEnum  $route = null,
        ?SourceEnum $source = null,
        ?int        $creditAmount = null
    )
    {
        $this->terminalKey = $terminalKey;
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->status = $status;
        $this->errorCode = $errorCode;
        $this->paymentId = $paymentId;
        $this->success = $success;

        if ($errorMessage) {
            $this->errorMessage = $errorMessage;
        }

        if ($errorDetails) {
            $this->errorDetails = $errorDetails;
        }

        if ($route) {
            $this->route = $route;
        }

        if ($source) {
            $this->source = $source;
        }

        if ($creditAmount) {
            $this->creditAmount = $creditAmount;
        }
    }
}