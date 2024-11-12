<?php

namespace Egorov\TinkoffApi\Domain\Enum;

enum PayTypeEnum: string
{
    case SINGLE_STAGE = 'O';

    case TWO_STAGE = 'T';
}
