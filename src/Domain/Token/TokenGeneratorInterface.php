<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Token;

interface TokenGeneratorInterface
{
    public function generate(array $data): string;
}
