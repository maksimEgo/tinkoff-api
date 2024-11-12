<?php

namespace Egorov\TinkoffApi\Domain\Token;

interface TokenGeneratorInterface
{
    public function generate(array $data): string;
}