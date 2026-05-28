<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Exception;

use RuntimeException;

class TinkoffApiException extends RuntimeException
{
    private string $errorCode;
    private string $errorDetails;

    public function __construct(string $message, string $errorCode, string $errorDetails = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        $this->errorDetails = $errorDetails;

        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorDetails(): string
    {
        return $this->errorDetails;
    }
}
