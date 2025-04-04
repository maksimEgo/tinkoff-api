<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Tests\TestHelper;

class TestCardProvider
{
    public static function getErrorPaymentCard(): array
    {
        return [
            'pan'         => '2201382000000021',
            'exp_date'    => '12/25',
            'cvv'         => '123',
            'description' => 'Ошибка при списании'
        ];
    }

    public static function getInsufficientFundsCard(): array
    {
        return [
            'pan'         => '2201382000000831',
            'exp_date'    => '12/25',
            'cvv'         => '123',
            'description' => 'Недостаточно средств'
        ];
    }

    public static function getSuccessful3ds2FrictionlessCard(): array
    {
        return [
            'pan'          => '2201382000000013',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'AUTHENTICATION_SUCCESSFUL',
            'description'  => 'Успешное прохождение аутентификации без ввода пароля'
        ];
    }

    public static function getSuccessful3ds2ChallengeCard(): array
    {
        return [
            'pan'          => '2201382000000047',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'CHALLENGE_REQURIED',
            'description'  => 'Требуется полное прохождение 3DS с редиректом',
            'otp_password' => '1qwezxc'
        ];
    }

    public static function getRestricted3ds2Card(): array
    {
        return [
            'pan'          => '2201382000000005',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'ACCOUNT_VERIFICATION_REJECTED',
            'description'  => 'Эмитент отклонил аутентификацию'
        ];
    }

    public static function getNotAuthenticated3ds2Card(): array
    {
        return [
            'pan'          => '2201382000000021',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'NOT_AUTHENTICATED',
            'description'  => 'Карта не поддерживает 3DS'
        ];
    }

    public static function getCardNotEnrolled3ds2Card(): array
    {
        return [
            'pan'          => '2201382000000039',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'ATTEMPTS_PROCESSING_PERFORMED',
            'description'  => 'Эмитент недоступен или не поддерживает 3DS v2.1'
        ];
    }

    public static function getSuccessfulNon3dsCard(): array
    {
        return [
            'pan'         => '2200770239097761',
            'exp_date'    => '12/25',
            'cvv'         => '123',
            'description' => 'Успешная оплата без 3DS'
        ];
    }

    public static function getMockSuccessfulCard(): array
    {
        return [
            'pan'          => '2201382000000591',
            'exp_date'     => '12/25',
            'cvv'          => '123',
            'trans_status' => 'AUTHENTICATION_SUCCESSFUL_REASON_18',
            'description'  => 'Успешное прохождение аутентификации без ввода пароля c заполненной transStatusReason'
        ];
    }

    public static function getAllCards(): array
    {
        return [
            'error_payment'                => self::getErrorPaymentCard(),
            'insufficient_funds'           => self::getInsufficientFundsCard(),
            'successful_3ds2_frictionless' => self::getSuccessful3ds2FrictionlessCard(),
            'successful_3ds2_challenge'    => self::getSuccessful3ds2ChallengeCard(),
            'restricted_3ds2'              => self::getRestricted3ds2Card(),
            'not_authenticated_3ds2'       => self::getNotAuthenticated3ds2Card(),
            'card_not_enrolled_3ds2'       => self::getCardNotEnrolled3ds2Card(),
            'successful_non_3ds'           => self::getSuccessfulNon3dsCard(),
            'mock_successful'              => self::getMockSuccessfulCard()
        ];
    }
}
