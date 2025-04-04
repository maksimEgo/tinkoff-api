<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentMethodEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentObjectEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxationEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxEnum;
use InvalidArgumentException;

class Receipt
{
    /** @var ReceiptItem[] */
    private array $items = [];

    private ?Customer $customer = null;

    private ?TaxationEnum $taxation = null;

    private ?array $payments = null;

    private ?array $vats = null;

    public function addItem(ReceiptItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function addItemFromArray(array $itemData): self
    {
        $requiredFields = ['Name', 'Price', 'Quantity', 'Amount', 'Tax', 'PaymentMethod', 'PaymentObject'];
        foreach ($requiredFields as $field) {
            if (!isset($itemData[$field])) {
                throw new InvalidArgumentException("Поле $field обязательно для товара");
            }
        }

        $item = new ReceiptItem(
            $itemData['Name'],
            $itemData['Price'],
            $itemData['Quantity'],
            $itemData['Amount'],
            TaxEnum::from($itemData['Tax']),
            PaymentMethodEnum::from($itemData['PaymentMethod']),
            PaymentObjectEnum::from($itemData['PaymentObject'])
        );

        if (isset($itemData['Ean13'])) {
            $item->withEan13($itemData['Ean13']);
        }

        if (isset($itemData['ShopCode'])) {
            $item->withShopCode($itemData['ShopCode']);
        }

        if (isset($itemData['AgentData'])) {
            $item->withAgentData($itemData['AgentData']);
        }

        if (isset($itemData['SupplierInfo'])) {
            $item->withSupplierInfo($itemData['SupplierInfo']);
        }

        $this->items[] = $item;
        return $this;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function setTaxation(TaxationEnum $taxation): self
    {
        $this->taxation = $taxation;
        return $this;
    }

    public function setTaxationFromString(string $taxation): self
    {
        $this->taxation = TaxationEnum::from($taxation);
        return $this;
    }

    public function setReceiptPayments(ReceiptPayments $payments): self
    {
        $totalAmount = array_reduce($this->items, function ($carry, ReceiptItem $item) {
            return $carry + $item->getAmount();
        }, 0);

        if (!$payments->validateTotal($totalAmount)) {
            throw new InvalidArgumentException("Общая сумма платежей не соответствует общей сумме товаров: {$totalAmount}");
        }

        $this->payments = $payments->toArray();
        return $this;
    }

    public function setVats(array $vats): self
    {
        $this->vats = $vats;
        return $this;
    }

    public function toArray(): array
    {
        if (empty($this->items)) {
            throw new InvalidArgumentException("Чек должен содержать хотя бы один товар");
        }

        if ($this->customer === null) {
            throw new InvalidArgumentException("Для чека обязательно указание покупателя");
        }

        if ($this->taxation === null) {
            throw new InvalidArgumentException("Для чека обязательно указание системы налогообложения");
        }

        $receipt = [
            'Items' => array_map(function(ReceiptItem $item) {
                return $item->toArray();
            }, $this->items),
            'Taxation' => $this->taxation->value
        ];

        if ($this->customer->getEmail() !== null) {
            $receipt['Email'] = $this->customer->getEmail();
        }

        if ($this->customer->getPhone() !== null) {
            $receipt['Phone'] = $this->customer->getPhone();
        }

        if (!isset($receipt['Email']) && !isset($receipt['Phone'])) {
            throw new InvalidArgumentException("Для покупателя должен быть указан Email или Phone");
        }

        if ($this->payments !== null) {
            $receipt['Payments'] = $this->payments;
        }

        if ($this->vats !== null) {
            $receipt['Vats'] = $this->vats;
        }

        return $receipt;
    }
}
