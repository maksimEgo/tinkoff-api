<?php

namespace Egorov\TinkoffApi\Domain\Enum;

enum RouteEnum: string
{
    case ACQ  = 'ACQ';
    case BNPL = 'BNPL';
    case TCB  = 'TCB';
    case SBER = 'SBER';
}
