<?php

declare(strict_types=1);

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

    public function withNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function withAccount(string $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function withDefaultCard(string $defaultCard): self
    {
        $this->defaultCard = $defaultCard;
        return $this;
    }

    public function withDevice(DeviceEnum $device): self
    {
        $this->device = $device;
        return $this;
    }

    public function withDeviceOs(DeviceOsEnum $deviceOs): self
    {
        $this->deviceOs = $deviceOs;
        return $this;
    }

    public function withDeviceBrowser(DeviceBrowserEnum $deviceBrowser): self
    {
        $this->deviceBrowser = $deviceBrowser;
        return $this;
    }

    public function withNotificationEnableSource(string $source): self
    {
        $this->notificationEnableSource = $source;
        return $this;
    }

    public function withQr(bool $qr): self
    {
        $this->qr = $qr;
        return $this;
    }

    public function getNumber(): ?string { return $this->number; }
    public function getEmail(): ?string { return $this->email; }
    public function getAccount(): ?string { return $this->account; }
    public function getDefaultCard(): ?string { return $this->defaultCard; }
    public function getDevice(): ?DeviceEnum { return $this->device; }
    public function getDeviceOs(): ?DeviceOsEnum { return $this->deviceOs; }
    public function getDeviceBrowser(): ?DeviceBrowserEnum { return $this->deviceBrowser; }
    public function getNotificationEnableSource(): ?string { return $this->notificationEnableSource; }
    public function getQr(): ?bool { return $this->qr; }

    public function toArray(): array
    {
        $result = [];

        if ($this->number !== null) $result['Number'] = $this->number;
        if ($this->email !== null) $result['Email'] = $this->email;
        if ($this->account !== null) $result['Account'] = $this->account;
        if ($this->defaultCard !== null) $result['DefaultCard'] = $this->defaultCard;
        if ($this->device !== null) $result['Device'] = $this->device->value;
        if ($this->deviceOs !== null) $result['DeviceOs'] = $this->deviceOs->value;
        if ($this->deviceBrowser !== null) $result['DeviceBrowser'] = $this->deviceBrowser->value;
        if ($this->notificationEnableSource !== null) $result['NotificationEnableSource'] = $this->notificationEnableSource;
        if ($this->qr !== null) $result['QR'] = $this->qr;

        return $result;
    }
}
