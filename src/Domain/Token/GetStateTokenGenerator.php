<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Token;

class GetStateTokenGenerator extends AbstractTokenGenerator
{
    protected array $requiredKeys = ['TerminalKey', 'PaymentId'];
}
