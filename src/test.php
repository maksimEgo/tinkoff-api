<?php

require __DIR__ . '/../vendor/autoload.php';

use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Enum\LanguageEnum;
use Egorov\TinkoffApi\Domain\ValueObject\Amount;
use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
use Egorov\TinkoffApi\Infrastructure\TinkoffClient;

$orderId = new OrderId('test_order_123_321');
$amount = new Amount(10000);

$order = Order::build($orderId, $amount)
    ->withDescription('Описание заказа')
    ->withLanguage(LanguageEnum::ENGLISH);

$terminalKey = 'TinkoffBankTest';
$password = 'TinkoffBankTest';

$tinkoffClient = new TinkoffClient($terminalKey, $password);

$payment = $tinkoffClient->initiatePayment($order);

var_dump($payment);

$status = $tinkoffClient->getPaymentStatus('5308622758');

var_dump($status);