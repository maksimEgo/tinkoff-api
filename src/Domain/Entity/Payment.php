<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Entity;

abstract class Payment
{
    protected string $status;

    public function getStatus(): string
    {
        return $this->status;
    }

    protected string $errorCode;

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    protected ?string $errorMessage;

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    protected ?string $errorDetails;

    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }
}
