<?php

declare(strict_types=1);

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

        if (strlen((string)$value) > 10) {
            throw new InvalidArgumentException('Amount must not exceed 10 digits.');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
