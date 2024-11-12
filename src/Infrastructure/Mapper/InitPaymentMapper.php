<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure\Mapper;

use Egorov\TinkoffApi\Domain\Entity\InitPayment;

class InitPaymentMapper
{
    public static function fromArray($data): InitPayment
    {
        return new InitPayment(
            terminalKey:  $data['TerminalKey'],
            amount:       $data['Amount'],
            orderId:      $data['OrderId'],
            status:       $data['Status'],
            errorCode:    $data['ErrorCode'],
            success:      $data['Success'],
            paymentId:    $data['PaymentId'],
            paymentURL:   $data['PaymentURL'] ?? null,
            errorMessage: $data['Message'] ?? null,
            errorDetails: $data['Details'] ?? null,
        );
    }
}
