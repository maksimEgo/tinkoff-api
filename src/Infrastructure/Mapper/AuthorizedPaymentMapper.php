<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure\Mapper;

use Egorov\TinkoffApi\Domain\Entity\AuthorizedPayment;

class AuthorizedPaymentMapper
{
    public static function fromArray(array $data): AuthorizedPayment
    {
        return new AuthorizedPayment(
            terminalKey:  $data['TerminalKey'],
            amount:       $data['Amount'],
            orderId:      $data['OrderId'],
            status:       $data['Status'],
            errorCode:    $data['ErrorCode'] ?? '0',
            success:      $data['Success'],
            paymentId:    $data['PaymentId'],
            errorMessage: $data['Message'] ?? null,
            errorDetails: $data['Details'] ?? null,
            rebillId:     $data['RebillId'] ?? null,
            cardId:       $data['CardId'] ?? null,
            acsUrl:       $data['ACSUrl'] ?? null,
            md:           $data['MD'] ?? null,
            paReq:        $data['PaReq'] ?? null,
        );
    }
}
