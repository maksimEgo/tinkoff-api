<?php

namespace Egorov\TinkoffApi\Domain\Token;

class GetStateTokenGenerator extends AbstractTokenGenerator
{
    protected array $requiredKeys = ['TerminalKey', 'PaymentId'];
}