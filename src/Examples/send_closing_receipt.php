<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Egorov\TinkoffApi\Domain\Entity\Customer;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Infrastructure\TinkoffClient;

if ($argc < 2) {
    die("Использование: php send_closing_receipt.php <paymentId>\n");
}

$paymentId = $argv[1];

$terminalKey = 'TinkoffBankTest';
$password    = 'TinkoffBankTest';

$tinkoffClient = new TinkoffClient($terminalKey, $password);

try {
    $paymentStatus = $tinkoffClient->getPaymentStatus($paymentId);

    if ($paymentStatus->getStatus() !== 'CONFIRMED') {
        die("Ошибка: Закрывающий чек можно отправить только для платежей в статусе CONFIRMED. Текущий статус: " .
            $paymentStatus->getStatus() . "\n");
    }

    $customer = new Customer();
    $customer->withEmail('customer@example.com');

    $receipt = new Receipt();

    $receipt->addItem([
        'Name'          => 'Наименование товара 1 (закрывающий чек)',
        'Price'         => 5000,
        'Quantity'      => 1.00,
        'Amount'        => 5000,
        'Tax'           => 'vat20',
        'PaymentMethod' => 'full_payment',
        'PaymentObject' => 'commodity'
    ]);

    $receipt->addItem([
        'Name'          => 'Наименование товара 2 (закрывающий чек)',
        'Price'         => 5000,
        'Quantity'      => 1.00,
        'Amount'        => 5000,
        'Tax'           => 'vat20',
        'PaymentMethod' => 'full_payment', // Используем полную оплату
        'PaymentObject' => 'commodity'
    ]);

    $receipt->setCustomer($customer);
    $receipt->setTaxation('osn');

    $result = $tinkoffClient->sendClosingReceipt($paymentId, $receipt);

    if ($result) {
        echo "=== ЗАКРЫВАЮЩИЙ ЧЕК ОТПРАВЛЕН ===\n";
        echo "Закрывающий чек успешно отправлен для платежа ID: {$paymentId}\n";
        echo "================================\n";
    } else {
        echo "Ошибка при отправке закрывающего чека\n";
    }

} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}