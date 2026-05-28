# Tinkoff API Integration

PHP-библиотека для интеграции с платёжным шлюзом T-Bank (ex-Tinkoff). Построена на принципах DDD и SOLID.

**Документация API:** https://developer.tbank.ru/eacq/intro/

## Установка

```sh
composer require egorov/tinkoff-api
```

Требования: PHP 7.4+, Guzzle 7/8.

## Быстрый старт

### Создание платежа

```php
use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\ValueObject\Amount;
use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
use Egorov\TinkoffApi\Infrastructure\TinkoffClient;

$client = new TinkoffClient('YourTerminalKey', 'YourPassword');

$order = Order::build(new OrderId('order-123'), new Amount(10000))
    ->withDescription('Оплата заказа #123');

$payment = $client->initiatePayment($order);

if ($payment->isSuccess()) {
    echo "URL оплаты: " . $payment->getPaymentURL();
}
```

### Создание платежа с чеком (54-ФЗ)

```php
use Egorov\TinkoffApi\Domain\Entity\Customer;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Domain\Entity\ReceiptItem;
use Egorov\TinkoffApi\Domain\Enum\LanguageEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentMethodEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\PaymentObjectEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxationEnum;
use Egorov\TinkoffApi\Domain\Enum\Receipt\TaxEnum;

$customer = new Customer();
$customer->withEmail('customer@example.com');

$item = new ReceiptItem(
    'Наименование товара',
    5000,          // цена в копейках
    1.00,          // количество
    5000,          // сумма в копейках
    TaxEnum::VAT20,
    PaymentMethodEnum::FULL_PAYMENT,
    PaymentObjectEnum::COMMODITY
);

$receipt = new Receipt();
$receipt->addItem($item);
$receipt->setCustomer($customer);
$receipt->setTaxation(TaxationEnum::OSN);

$order = Order::build(new OrderId('order-456'), new Amount(5000))
    ->withDescription('Заказ с чеком')
    ->withLanguage(LanguageEnum::RUSSIAN)
    ->withReceipt($receipt);

$payment = $client->initiatePayment($order);
```

### Проверка статуса платежа

```php
$status = $client->getPaymentStatus($paymentId);

echo "Статус: " . $status->getStatus(); // NEW, AUTHORIZED, CONFIRMED, CANCELED, ...
```

### Подтверждение двухстадийного платежа

```php
$order = Order::build($orderId, $amount)
    ->withPayType(\Egorov\TinkoffApi\Domain\Enum\PayTypeEnum::TWO_STAGE);

$payment = $client->initiatePayment($order);

// После оплаты клиентом (статус AUTHORIZED):
$confirmed = $client->confirmPayment($paymentId);
```

### Отмена / возврат

```php
// Полная отмена
$result = $client->cancelPayment($paymentId);

// Частичный возврат (3000 копеек = 30 рублей)
$result = $client->cancelPayment($paymentId, 3000);
```

### Рекуррентные платежи

```php
// Первый платёж с привязкой карты
$order = Order::build($orderId, $amount)
    ->withCustomerKey('customer-1')
    ->withRecurrent('Y');

$payment = $client->initiatePayment($order);

// Последующие списания по RebillId
$newOrder = Order::build(new OrderId('recurring-1'), new Amount(5000));
$initPayment = $client->initiatePayment($newOrder);
$charge = $client->chargePayment($initPayment->getPaymentId(), $rebillId);
```

### Проверка заказа по OrderId

```php
$payments = $client->checkOrder('order-123');

foreach ($payments as $payment) {
    echo $payment->getStatus() . "\n";
}
```

### Закрывающий чек

```php
$receipt = new Receipt();
$receipt->addItem($item);
$receipt->setCustomer($customer);
$receipt->setTaxation(TaxationEnum::OSN);

$result = $client->sendClosingReceipt($paymentId, $receipt);
```

### Управление покупателями

```php
$client->addCustomer('customer-1', 'email@example.com', '+79001234567');
$data = $client->getCustomer('customer-1');
$client->removeCustomer('customer-1');
```

### Управление картами

```php
$cards = $client->getCardList('customer-1');
$client->removeCard('customer-1', $cardId);
```

### Переотправка уведомлений

```php
$result = $client->resend();
```

## Верификация вебхуков

T-Bank отправляет POST-уведомления на `NotificationURL`. Для проверки подписи:

```php
use Egorov\TinkoffApi\Infrastructure\NotificationVerifier;

$verifier = new NotificationVerifier('YourPassword');

// Вариант 1: проверка и парсинг из raw body
$data = $verifier->verifyAndParse(file_get_contents('php://input'));

// Вариант 2: проверка массива
$data = json_decode($requestBody, true);
if ($verifier->verify($data)) {
    // Подпись верна
}
```

Ответ на уведомление должен быть `200` с телом `OK`.

## Обработка ошибок

Библиотека выбрасывает три типа исключений:

```php
use Egorov\TinkoffApi\Domain\Exception\TinkoffApiException;
use Egorov\TinkoffApi\Domain\Exception\HttpException;
use Egorov\TinkoffApi\Domain\Exception\InvalidResponseException;

try {
    $payment = $client->initiatePayment($order);
} catch (TinkoffApiException $e) {
    // Ошибка API: $e->getMessage(), $e->getErrorCode(), $e->getErrorDetails()
} catch (HttpException $e) {
    // Сетевая ошибка
} catch (InvalidResponseException $e) {
    // Невалидный JSON в ответе
}
```

## Суммы

Все суммы указываются в **копейках**. `new Amount(10000)` = 100 рублей.

## Лицензия

MIT
