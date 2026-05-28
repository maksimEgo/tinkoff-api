<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Token\TokenGenerator;

class NotificationVerifier
{
    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Проверяет подпись входящего уведомления от T-Bank.
     * Возвращает true, если Token в уведомлении корректен.
     *
     * @param array $notificationData Данные уведомления (декодированный JSON)
     */
    public function verify(array $notificationData): bool
    {
        if (!isset($notificationData['Token'])) {
            return false;
        }

        $receivedToken = $notificationData['Token'];

        $tokenGenerator = new TokenGenerator($this->password);
        $expectedToken = $tokenGenerator->generate($notificationData);

        return hash_equals($expectedToken, $receivedToken);
    }

    /**
     * Проверяет подпись и возвращает данные уведомления из JSON-строки.
     * Выбрасывает исключение при невалидной подписи.
     *
     * @param string $requestBody Raw JSON body из запроса
     * @return array Данные уведомления
     * @throws \RuntimeException При невалидном JSON или подписи
     */
    public function verifyAndParse(string $requestBody): array
    {
        $data = json_decode($requestBody, true);

        if (!is_array($data)) {
            throw new \RuntimeException('Invalid notification JSON');
        }

        if (!$this->verify($data)) {
            throw new \RuntimeException('Invalid notification token');
        }

        return $data;
    }
}
