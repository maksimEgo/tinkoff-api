<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure\Mapper;

use Egorov\TinkoffApi\Domain\Entity\StatePayment;
use Egorov\TinkoffApi\Domain\Enum\RouteEnum;
use Egorov\TinkoffApi\Domain\Enum\SourceEnum;

class StatePaymentMapper
{
    public static function fromArray($data): StatePayment
    {
        return new StatePayment(
            terminalKey:  $data['TerminalKey'],
            amount:       $data['Amount'],
            orderId:      $data['OrderId'],
            status:       $data['Status'],
            errorCode:    $data['ErrorCode'],
            success:      $data['Success'],
            paymentId:    $data['PaymentId'],
            errorMessage: $data['Message'] ?? null,
            errorDetails: $data['Details'] ?? null,
            route:        isset($data['Route']) ? RouteEnum::tryFrom($data['Route']) : null,
            source:       isset($data['Source']) ? SourceEnum::tryFrom($data['Source']) : null,
            creditAmount: $data['CreditAmount'] ?? null,
        );
    }
}
