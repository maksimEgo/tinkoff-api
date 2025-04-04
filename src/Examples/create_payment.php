<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Egorov\TinkoffApi\Domain\Entity\Customer;
use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Domain\Entity\ReceiptItem;
use Egorov\TinkoffApi\Domain\Enum\LanguageEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentMethodEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentObjectEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxationEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxEnum;
use Egorov\TinkoffApi\Domain\ValueObject\Amount;
use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
use Egorov\TinkoffApi\Infrastructure\TinkoffClient;

$orderId = new OrderId('test_order_' . time());
$amount = new Amount(5000);

$customer = new Customer();
$customer->withEmail('customer@example.com');

$receipt = new Receipt();

$item1 = new ReceiptItem(
    'Наименование товара 1',
    5000,
    1.00,
    5000,
    TaxEnum::VAT20,
    PaymentMethodEnum::FULL_PREPAYMENT,
    PaymentObjectEnum::COMMODITY
);

$receipt->addItem($item1);

$receipt->setCustomer($customer);
$receipt->setTaxation(TaxationEnum::OSN);

$order = Order::build($orderId, $amount)
    ->withDescription('Тестовый заказ с предоплатой')
    ->withLanguage(LanguageEnum::RUSSIAN)
    ->withReceipt($receipt);

$terminalKey = 'TinkoffBankTest';
$password = 'TinkoffBankTest';

$tinkoffClient = new TinkoffClient($terminalKey, $password);

try {
    $payment = $tinkoffClient->initiatePayment($order);

    echo "=== ПЛАТЕЖ СОЗДАН ===\n";
    echo "Payment URL: " . $payment->getPaymentURL() . "\n";
    echo "Payment ID: " . $payment->getPaymentId() . "\n";
    echo "Status: " . $payment->getStatus() . "\n";
    echo "=====================\n\n";

} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}