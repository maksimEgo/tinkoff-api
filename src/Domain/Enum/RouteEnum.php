<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum;

enum RouteEnum: string
{
    case ACQ  = 'ACQ';
    case BNPL = 'BNPL';
    case TCB  = 'TCB';
    case SBER = 'SBER';
}
