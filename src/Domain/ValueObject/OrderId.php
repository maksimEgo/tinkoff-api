<?php

namespace Egorov\TinkoffApi\Domain\ValueObject;

class OrderId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Order ID cannot be empty.');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
