<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentMethodEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentObjectEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxEnum;
use InvalidArgumentException;

class ReceiptItem
{
    private string $name;

    private int $price;

    private float $quantity;

    private int $amount;

    private TaxEnum $tax;

    private PaymentMethodEnum $paymentMethod;

    private PaymentObjectEnum $paymentObject;

    private ?string $ean13 = null;

    private ?string $shopCode = null;

    private ?array $agentData = null;

    private ?array $supplierInfo = null;

    public function __construct(
        string $name,
        int $price,
        float $quantity,
        int $amount,
        TaxEnum $tax,
        PaymentMethodEnum $paymentMethod,
        PaymentObjectEnum $paymentObject
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('Наименование товара не может быть пустым');
        }

        if ($price < 0) {
            throw new InvalidArgumentException('Цена товара не может быть отрицательной');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Количество товара должно быть положительным');
        }

        if ($amount < 0) {
            throw new InvalidArgumentException('Сумма не может быть отрицательной');
        }

        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->amount = $amount;
        $this->tax = $tax;
        $this->paymentMethod = $paymentMethod;
        $this->paymentObject = $paymentObject;
    }

    public function withEan13(string $ean13): self
    {
        if (!preg_match('/^\d{13}$/', $ean13)) {
            throw new InvalidArgumentException('Штрихкод должен содержать 13 цифр');
        }

        $this->ean13 = $ean13;
        return $this;
    }

    public function withShopCode(string $shopCode): self
    {
        $this->shopCode = $shopCode;
        return $this;
    }

    public function withAgentData(array $agentData): self
    {
        $this->agentData = $agentData;
        return $this;
    }

    public function withSupplierInfo(array $supplierInfo): self
    {
        $this->supplierInfo = $supplierInfo;
        return $this;
    }

    public function toArray(): array
    {
        $result = [
            'Name'          => $this->name,
            'Price'         => $this->price,
            'Quantity'      => $this->quantity,
            'Amount'        => $this->amount,
            'Tax'           => $this->tax->value,
            'PaymentMethod' => $this->paymentMethod->value,
            'PaymentObject' => $this->paymentObject->value
        ];

        if ($this->ean13 !== null) {
            $result['Ean13'] = $this->ean13;
        }

        if ($this->shopCode !== null) {
            $result['ShopCode'] = $this->shopCode;
        }

        if ($this->agentData !== null) {
            $result['AgentData'] = $this->agentData;
        }

        if ($this->supplierInfo !== null) {
            $result['SupplierInfo'] = $this->supplierInfo;
        }

        return $result;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}