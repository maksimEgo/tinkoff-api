<?php

namespace Egorov\TinkoffApi\Domain\Enum\Data;

enum DeviceOsEnum: string
{
    case iOS = 'iOS';

    case Android = 'Android';

    case macOS = 'macOS';

    case Windows = 'Windows';

    case Linux = 'Linux';
}
