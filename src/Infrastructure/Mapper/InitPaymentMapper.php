<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure\Mapper;

use Egorov\TinkoffApi\Domain\Entity\InitPayment;

class InitPaymentMapper
{
    public static function fromArray(array $response): InitPayment
    {
        $requiredFields = ['TerminalKey', 'Amount', 'OrderId', 'Status', 'PaymentId', 'Success'];

        foreach ($requiredFields as $field) {
            if (!isset($response[$field])) {
                error_log("Missing required field in response: {$field}");
                error_log("Response content: " . json_encode($response, JSON_UNESCAPED_UNICODE));
                throw new \RuntimeException("Missing required field in response: {$field}");
            }
        }

        return new InitPayment(
            $response['TerminalKey'],
            $response['Amount'],
            $response['OrderId'],
            $response['Status'],
            $response['ErrorCode'] ?? '0',
            $response['Success'],
            $response['PaymentId'],
            $response['PaymentURL'] ?? null,
            $response['Message'] ?? null,
            $response['Details'] ?? null
        );
    }
}
