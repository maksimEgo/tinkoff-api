<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Enum\Receipt;

enum PaymentObjectEnum: string
{
    case COMMODITY = 'commodity';
    case EXCISE = 'excise';
    case JOB = 'job';
    case SERVICE = 'service';
    case GAMBLING_BET = 'gambling_bet';
    case GAMBLING_PRIZE = 'gambling_prize';
    case LOTTERY = 'lottery';
    case LOTTERY_PRIZE = 'lottery_prize';
    case INTELLECTUAL_ACTIVITY = 'intellectual_activity';
    case PAYMENT = 'payment';
    case AGENT_COMMISSION = 'agent_commission';
    case COMPOSITE = 'composite';
    case ANOTHER = 'another';
    case PROPERTY_RIGHT = 'property_right';
    case NON_OPERATING_GAIN = 'non-operating_gain';
    case INSURANCE_PREMIUM = 'insurance_premium';
    case SALES_TAX = 'sales_tax';
    case RESORT_FEE = 'resort_fee';
    case DEPOSIT = 'deposit';

    public function getDescription(): string
    {
        return match($this) {
            self::COMMODITY             => 'Товар',
            self::EXCISE                => 'Подакцизный товар',
            self::JOB                   => 'Работа',
            self::SERVICE               => 'Услуга',
            self::GAMBLING_BET          => 'Ставка в азартной игре',
            self::GAMBLING_PRIZE        => 'Выигрыш в азартной игре',
            self::LOTTERY               => 'Лотерейный билет',
            self::LOTTERY_PRIZE         => 'Выигрыш в лотерею',
            self::INTELLECTUAL_ACTIVITY => 'Результаты интеллектуальной деятельности',
            self::PAYMENT               => 'Платеж',
            self::AGENT_COMMISSION      => 'Агентское вознаграждение',
            self::COMPOSITE             => 'Составной предмет расчета',
            self::ANOTHER               => 'Иной предмет расчета',
            self::PROPERTY_RIGHT        => 'Имущественное право',
            self::NON_OPERATING_GAIN    => 'Внереализационный доход',
            self::INSURANCE_PREMIUM     => 'Страховой взнос',
            self::SALES_TAX             => 'Торговый сбор',
            self::RESORT_FEE            => 'Курортный сбор',
            self::DEPOSIT               => 'Залог'
        };
    }
}
