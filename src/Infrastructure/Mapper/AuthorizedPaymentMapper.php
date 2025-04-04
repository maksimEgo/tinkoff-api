<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure\Mapper;

use Egorov\TinkoffApi\Domain\Entity\AuthorizedPayment;
use Egorov\TinkoffApi\Domain\Entity\Payment;

/*
 * in development
 */
class AuthorizedPaymentMapper
{
    public static function fromArray(array $data): Payment
    {
        $payment = new AuthorizedPayment();

        if (isset($data['TerminalKey'])) {
            $payment->setTerminalKey($data['TerminalKey']);
        }

        if (isset($data['Amount'])) {
            $payment->setAmount($data['Amount']);
        }

        if (isset($data['OrderId'])) {
            $payment->setOrderId($data['OrderId']);
        }

        if (isset($data['Success'])) {
            $payment->setSuccess($data['Success']);
        }

        if (isset($data['Status'])) {
            $payment->setStatus($data['Status']);
        }

        if (isset($data['PaymentId'])) {
            $payment->setPaymentId($data['PaymentId']);
        }

        if (isset($data['ErrorCode'])) {
            $payment->setErrorCode($data['ErrorCode']);
        }

        if (isset($data['Message'])) {
            $payment->setErrorMessage($data['Message']);
        }

        if (isset($data['Details'])) {
            $payment->setErrorDetails($data['Details']);
        }

        if (isset($data['RebillId'])) {
            $payment->setRebillId($data['RebillId']);
        }

        if (isset($data['CardId'])) {
            $payment->setCardId($data['CardId']);
        }

        return $payment;
    }
}