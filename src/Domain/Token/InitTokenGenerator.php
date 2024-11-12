<?php

namespace Egorov\TinkoffApi\Domain\Token;

class InitTokenGenerator extends AbstractTokenGenerator
{
    protected array $requiredKeys = ['Amount', 'OrderId', 'TerminalKey', 'Description'];
}