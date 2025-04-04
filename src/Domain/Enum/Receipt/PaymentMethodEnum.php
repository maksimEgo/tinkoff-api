<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum\Receipt;

enum PaymentMethodEnum: string
{
    case FULL_PREPAYMENT = 'full_prepayment';
    case PREPAYMENT = 'prepayment';
    case ADVANCE = 'advance';
    case FULL_PAYMENT = 'full_payment';
    case PARTIAL_PAYMENT = 'partial_payment';
    case CREDIT = 'credit';
    case CREDIT_PAYMENT = 'credit_payment';

    public function getDescription(): string
    {
        return match($this) {
            self::FULL_PREPAYMENT => 'Полная предоплата',
            self::PREPAYMENT      => 'Частичная предоплата',
            self::ADVANCE         => 'Аванс',
            self::FULL_PAYMENT    => 'Полный расчет',
            self::PARTIAL_PAYMENT => 'Частичный расчет и кредит',
            self::CREDIT          => 'Передача в кредит',
            self::CREDIT_PAYMENT  => 'Оплата кредита'
        };
    }
}