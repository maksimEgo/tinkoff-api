<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\ValueObject;

use InvalidArgumentException;

class OrderId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Order ID cannot be empty.');
        }

        if (strlen($value) > 36) {
            throw new InvalidArgumentException('Amount must not exceed 36 digits.');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
