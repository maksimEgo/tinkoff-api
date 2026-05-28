<?php

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;
use Egorov\TinkoffApi\Domain\Entity\Receipt;

interface PaymentClientInterface
{
    /**
     * Инициирует платеж на основе объекта заказа
     */
    public function initiatePayment(Order $order): Payment;

    /**
     * Проверяет статус платежа
     */
    public function getPaymentStatus(string $paymentId, ?string $clientIp = null): Payment;

    /**
     * Отправляет закрывающий чек в кассу
     */
    public function sendClosingReceipt(string $paymentId, Receipt $receipt): bool;

    /**
     * Подтверждает платеж с передачей реквизитов карты (требуется PCI DSS)
     */
    public function finishAuthorize(
        string $paymentId,
        array $cardData,
        ?string $ip = null,
        ?array $additionalParams = null
    ): Payment;

    /**
     * Подтверждает списание для двухстадийного платежа в статусе AUTHORIZED
     */
    public function confirmPayment(string $paymentId, ?int $amount = null, ?Receipt $receipt = null): Payment;

    /**
     * Отменяет или возвращает платеж (полностью или частично)
     */
    public function cancelPayment(string $paymentId, ?int $amount = null, ?Receipt $receipt = null): Payment;

    /**
     * Выполняет рекуррентный платеж по сохранённой карте
     */
    public function chargePayment(string $paymentId, string $rebillId): Payment;

    /**
     * Возвращает статус заказа по OrderId (массив платежей)
     */
    public function checkOrder(string $orderId): array;

    /**
     * Регистрирует покупателя
     */
    public function addCustomer(string $customerKey, ?string $email = null, ?string $phone = null): array;

    /**
     * Возвращает данные покупателя
     */
    public function getCustomer(string $customerKey): array;

    /**
     * Удаляет покупателя
     */
    public function removeCustomer(string $customerKey): array;

    /**
     * Возвращает список привязанных карт покупателя
     */
    public function getCardList(string $customerKey): array;

    /**
     * Удаляет привязанную карту покупателя
     */
    public function removeCard(string $customerKey, string $cardId): array;

    /**
     * Повторно отправляет недоставленные уведомления
     */
    public function resend(): array;
}