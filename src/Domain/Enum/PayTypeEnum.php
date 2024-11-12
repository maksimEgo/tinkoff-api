<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum;

enum PayTypeEnum: string
{
    case SINGLE_STAGE = 'O';

    case TWO_STAGE = 'T';
}
