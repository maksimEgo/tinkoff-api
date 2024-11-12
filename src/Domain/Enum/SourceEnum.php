<?php

namespace Egorov\TinkoffApi\Domain\Enum;

enum SourceEnum: string
{
    case BNPL        = 'BNPL';
    case CARDS       = 'cards';
    case INSTALLMENT = 'Installment';
    case MIRPAY      = 'MirPay';
    case QRSBP       = 'qrsbp';
    case SBERPAY     = 'SberPay';
    case TINKOFFPAY  = 'TinkoffPay';
    case YANDEXPAY   = 'YandexPay';
}
