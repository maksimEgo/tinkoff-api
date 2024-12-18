<?php

namespace Egorov\TinkoffApi\Domain\Entity;

use Egorov\TinkoffApi\Domain\Enum\Data\DeviceBrowserEnum;
use Egorov\TinkoffApi\Domain\Enum\Data\DeviceEnum;
use Egorov\TinkoffApi\Domain\Enum\Data\DeviceOsEnum;

class OrderData
{
    private ?string $number = null;

    private ?string $email = null;

    private ?string $account = null;

    private ?string $defaultCard = null;

    private ?DeviceEnum $device = null;

    private ?DeviceOsEnum $deviceOs = null;

    private ?DeviceBrowserEnum $deviceBrowser = null;

    private ?string $notificationEnableSource = null;

    private ?bool $qr = null;
}
