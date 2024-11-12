<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Token;

class InitTokenGenerator extends AbstractTokenGenerator
{
    protected array $requiredKeys = ['Amount', 'OrderId', 'TerminalKey', 'Description'];
}
