<?php

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;
use Egorov\TinkoffApi\Domain\Entity\Receipt;

interface PaymentClientInterface
{
    /**
     * Инициирует платеж на основе объекта заказа
     *
     * @param Order $order Объект заказа
     * @return Payment Объект платежа с информацией о созданной сессии
     */
    public function initiatePayment(Order $order): Payment;

    /**
     * Проверяет статус платежа
     *
     * @param string $paymentId Идентификатор платежа в системе Тинькофф
     * @param string|null $clientIp IP-адрес клиента (опционально)
     * @return Payment Объект платежа с информацией о текущем статусе
     */
    public function getPaymentStatus(string $paymentId, ?string $clientIp = null): Payment;

    /**
     * Отправляет закрывающий чек в кассу
     *
     * @param string $paymentId Идентификатор платежа в системе Тинькофф
     * @param Receipt $receipt Объект чека с данными товаров
     * @return bool Результат операции
     */
    public function sendClosingReceipt(string $paymentId, Receipt $receipt): bool;

    /**
     * Подтверждает платеж с передачей реквизитов карты
     * Используется только при наличии PCI DSS сертификации и собственной платежной формы
     *
     * @param string $paymentId Идентификатор платежа
     * @param array $cardData Данные карты (зашифрованные)
     * @param string|null $ip IP-адрес клиента
     * @param array|null $additionalParams Дополнительные параметры
     * @return Payment Объект платежа с информацией о результате операции
     */
    public function finishAuthorize(
        string $paymentId,
        array $cardData,
        ?string $ip = null,
        ?array $additionalParams = null
    ): Payment;
}