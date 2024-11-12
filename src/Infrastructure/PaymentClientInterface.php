<?php

namespace Egorov\TinkoffApi\Infrastructure;

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Payment;

interface PaymentClientInterface
{
    public function initiatePayment(Order $order): Payment;
    public function getPaymentStatus(string $paymentId, ?string $clientIp = null): Payment;
}