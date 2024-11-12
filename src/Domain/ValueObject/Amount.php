<?php

namespace Egorov\TinkoffApi\Domain\ValueObject;

use InvalidArgumentException;

class Amount
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
