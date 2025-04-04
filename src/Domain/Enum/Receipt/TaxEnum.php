<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum\Receipt;

enum TaxEnum: string
{
    case NONE = 'none';
    case VAT0 = 'vat0';
    case VAT5 = 'vat5';
    case VAT7 = 'vat7';
    case VAT10 = 'vat10';
    case VAT20 = 'vat20';
    case VAT105 = 'vat105';
    case VAT107 = 'vat107';
    case VAT110 = 'vat110';
    case VAT120 = 'vat120';

    public function getDescription(): string
    {
        return match($this) {
            self::NONE   => 'Без НДС',
            self::VAT0   => 'НДС по ставке 0%',
            self::VAT5   => 'НДС по ставке 5%',
            self::VAT7   => 'НДС по ставке 7%',
            self::VAT10  => 'НДС по ставке 10%',
            self::VAT20  => 'НДС по ставке 20%',
            self::VAT105 => 'НДС по расчетной ставке 5/105',
            self::VAT107 => 'НДС по расчетной ставке 7/107',
            self::VAT110 => 'НДС по расчетной ставке 10/110',
            self::VAT120 => 'НДС по расчетной ставке 20/120'
        };
    }

    public function getRate(): float
    {
        return match($this) {
            self::NONE   => 0,
            self::VAT0   => 0,
            self::VAT5   => 5,
            self::VAT7   => 7,
            self::VAT10  => 10,
            self::VAT20  => 20,
            self::VAT105 => 5/105,
            self::VAT107 => 7/107,
            self::VAT110 => 10/110,
            self::VAT120 => 20/120
        };
    }
}