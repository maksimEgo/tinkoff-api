<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

class ReceiptPayments
{
    private ?int $cash = null;

    private int $electronic;

    private ?int $advancePayment = null;

    private ?int $credit = null;

    private ?int $provision = null;

    public function __construct(int $electronic)
    {
        $this->electronic = $electronic;
    }

    public function withCash(int $cash): self
    {
        $this->cash = $cash;
        return $this;
    }

    public function withAdvancePayment(int $advancePayment): self
    {
        $this->advancePayment = $advancePayment;
        return $this;
    }

    public function withCredit(int $credit): self
    {
        $this->credit = $credit;
        return $this;
    }

    public function withProvision(int $provision): self
    {
        $this->provision = $provision;
        return $this;
    }

    public function validateTotal(int $expectedTotal): bool
    {
        $actualTotal = $this->electronic;

        if ($this->cash !== null) {
            $actualTotal += $this->cash;
        }

        if ($this->advancePayment !== null) {
            $actualTotal += $this->advancePayment;
        }

        if ($this->credit !== null) {
            $actualTotal += $this->credit;
        }

        if ($this->provision !== null) {
            $actualTotal += $this->provision;
        }

        return $actualTotal === $expectedTotal;
    }

    public function toArray(): array
    {
        $result = [
            'Electronic' => $this->electronic
        ];

        if ($this->cash !== null) {
            $result['Cash'] = $this->cash;
        }

        if ($this->advancePayment !== null) {
            $result['AdvancePayment'] = $this->advancePayment;
        }

        if ($this->credit !== null) {
            $result['Credit'] = $this->credit;
        }

        if ($this->provision !== null) {
            $result['Provision'] = $this->provision;
        }

        return $result;
    }
}
