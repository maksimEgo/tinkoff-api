<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Egorov\TinkoffApi\Infrastructure\TinkoffClient;

if ($argc < 2) {
    die("Использование: php check_status.php <paymentId>\n");
}

$paymentId = $argv[1];

$terminalKey = 'TinkoffBankTest';
$password = 'TinkoffBankTest';

$tinkoffClient = new TinkoffClient($terminalKey, $password);

try {
    $paymentStatus = $tinkoffClient->getPaymentStatus($paymentId);

    echo "=== СТАТУС ПЛАТЕЖА ===\n";
    echo "Payment ID: {$paymentId}\n";
    echo "Статус: " . $paymentStatus->getStatus() . "\n";

    if ($paymentStatus->isSuccess()) {
        echo "Операция успешна\n";

        if ($paymentStatus->getStatus() === 'CONFIRMED') {
            echo "Платеж подтвержден, можно отправить закрывающий чек\n";
            echo "Запустите: php src/send_closing_receipt.php {$paymentId}\n";
        } elseif ($paymentStatus->getStatus() === 'AUTHORIZED') {
            echo "Платеж авторизован, ожидает подтверждения\n";
        } elseif ($paymentStatus->getStatus() === 'NEW') {
            echo "Платеж создан, ожидает оплаты\n";
        } elseif ($paymentStatus->getStatus() === 'REJECTED') {
            echo "Платеж отклонен\n";
        }
    } else {
        echo "Ошибка: " . $paymentStatus->getErrorMessage() . "\n";
        if ($paymentStatus->getErrorDetails()) {
            echo "Детали: " . $paymentStatus->getErrorDetails() . "\n";
        }
    }
    echo "=====================\n";

} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}