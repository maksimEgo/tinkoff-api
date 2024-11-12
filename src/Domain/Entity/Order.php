<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

use DateTimeImmutable;
use Egorov\TinkoffApi\Domain\Enum\LanguageEnum;
use Egorov\TinkoffApi\Domain\Enum\PayTypeEnum;
use Egorov\TinkoffApi\Domain\ValueObject\Amount;
use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
use InvalidArgumentException;

class Order
{
    private OrderId $orderId;

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    private Amount $amount;

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    private ?string $customerKey = null;

    public function getCustomerKey(): ?string
    {
        return $this->customerKey;
    }

    private ?string $recurrent = null;

    public function getRecurrent(): ?string
    {
        return $this->recurrent;
    }

    private ?PayTypeEnum $payType = null;

    public function getPayType(): ?PayTypeEnum
    {
        return $this->payType;
    }

    private ?LanguageEnum $language = null;

    public function getLanguage(): ?LanguageEnum
    {
        return $this->language;
    }

    private ?string $notificationURL = null;

    public function getNotificationURL(): ?string
    {
        return $this->notificationURL;
    }

    private ?string $successURL = null;

    public function getSuccessURL(): ?string
    {
        return $this->successURL;
    }

    private ?string $failURL = null;

    public function getFailURL(): ?string
    {
        return $this->failURL;
    }

    private ?DateTimeImmutable $redirectDueDate = null;

    public function getRedirectDueDate(): ?DateTimeImmutable
    {
        return $this->redirectDueDate;
    }

    public static function build(OrderId $orderId, Amount $amount): self
    {
        $order = new self();
        $order->orderId = $orderId;
        $order->amount = $amount;
        return $order;
    }

    public function withDescription(string $description): self
    {
        if (strlen($description) > 140) {
            throw new InvalidArgumentException('Amount must not exceed 140 digits.');
        }

        $this->description = $description;
        return $this;
    }

    public function withCustomerKey(string $customerKey): self
    {
        $this->customerKey = $customerKey;
        return $this;
    }

    public function withRecurrent(string $recurrent): self
    {
        if (strlen($recurrent) > 1) {
            throw new InvalidArgumentException('Amount must not exceed 1 digits.');
        }

        $this->recurrent = $recurrent;
        return $this;
    }

    public function withPayType(PayTypeEnum $payType): self
    {
        $this->payType = $payType;
        return $this;
    }

    public function withLanguage(LanguageEnum $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function withNotificationURL(string $notificationURL): self
    {
        $this->notificationURL = $notificationURL;
        return $this;
    }

    public function withSuccessURL(string $successURL): self
    {
        $this->successURL = $successURL;
        return $this;
    }

    public function withFailURL(string $failURL): self
    {
        $this->failURL = $failURL;
        return $this;
    }

    public function withRedirectDueDate(string $redirectDueDate): self
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/', $redirectDueDate)) {
            throw new InvalidArgumentException('Invalid RedirectDueDate format. Expected format: YYYY-MM-DDTHH:MM:SS+GMT');
        }

        try {
            $redirectDueDateObject = new DateTimeImmutable($redirectDueDate);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid date provided: ' . $e->getMessage());
        }

        $now = new DateTimeImmutable();
        $minDueDate = $now->modify('+1 minute');
        $maxDueDate = $now->modify('+90 days');

        if ($redirectDueDateObject < $minDueDate) {
            throw new InvalidArgumentException('RedirectDueDate must be at least 1 minute from the current date.');
        }

        if ($redirectDueDateObject > $maxDueDate) {
            throw new InvalidArgumentException('RedirectDueDate cannot be more than 90 days from the current date.');
        }

        $this->redirectDueDate = $redirectDueDateObject;

        return $this;
    }
}
