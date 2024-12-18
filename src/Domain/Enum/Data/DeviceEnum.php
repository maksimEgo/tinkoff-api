<?php

namespace Egorov\TinkoffApi\Domain\Enum\Data;

enum DeviceEnum: string
{
    case SDK = 'SDK';

    case Mobile = 'Mobile';

    case Desktop = 'Desktop';
}
