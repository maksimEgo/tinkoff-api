<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Tests\Integration;

use Egorov\TinkoffApi\Domain\Entity\Customer;
use Egorov\TinkoffApi\Domain\Entity\Order;
use Egorov\TinkoffApi\Domain\Entity\Receipt;
use Egorov\TinkoffApi\Domain\Enum\LanguageEnum;
use Egorov\TinkoffApi\Domain\ValueObject\Amount;
use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
use Egorov\TinkoffApi\Infrastructure\TinkoffClient;
use Egorov\TinkoffApi\Tests\TestHelper\TestCardProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TinkoffClientTest extends TestCase
{
    private TinkoffClient $tinkoffClient;
    private string $terminalKey;
    private string $password;
    private string $email;

    protected function setUp(): void
    {
        $this->terminalKey = 'TinkoffBankTest';
        $this->password    = 'TinkoffBankTest';
        $this->email       = 'dmitrijkislyj771@gmail.com';
        $testApiUrl        = 'https://securepay.tinkoff.ru/v2/';

        $this->tinkoffClient = new TinkoffClient(
            $this->terminalKey,
            $this->password,
            $testApiUrl
        );
    }

    #[Test]
    public function it_can_initialize_payment_with_receipt(): void
    {
        $orderId = new OrderId('test_' . uniqid());
        $amount = new Amount(10000);

        $customer = new Customer();
        $customer->withEmail($this->email);

        $receipt = new Receipt();
        $receipt->addItem([
            'Name' => 'Тестовый товар',
            'Price' => 10000,
            'Quantity' => 1.00,
            'Amount' => 10000,
            'Tax' => 'vat20',
            'PaymentMethod' => 'full_payment',
            'PaymentObject' => 'commodity'
        ]);
        $receipt->setCustomer($customer);
        $receipt->setTaxation('osn');

        $order = Order::build($orderId, $amount)
            ->withDescription('Тестовый заказ')
            ->withLanguage(LanguageEnum::RUSSIAN)
            ->withReceipt($receipt);

        $payment = $this->tinkoffClient->initiatePayment($order);

        $this->assertNotNull($payment->getPaymentId());
        $this->assertNotEmpty($payment->getPaymentURL());
        $this->assertTrue($payment->isSuccess());
    }

    #[Test]
    #[DataProvider('successfulPaymentCardsProvider')]
    public function it_can_initialize_payments_with_successful_cards(array $cardData): void
    {
        $orderId = new OrderId('test_' . uniqid());
        $amount = new Amount(10000);

        $customer = new Customer();
        $customer->withEmail($this->email);

        $receipt = new Receipt();
        $receipt->addItem([
            'Name' => 'Тестовый товар',
            'Price' => 10000,
            'Quantity' => 1.00,
            'Amount' => 10000,
            'Tax' => 'vat20',
            'PaymentMethod' => 'full_payment',
            'PaymentObject' => 'commodity'
        ]);
        $receipt->setCustomer($customer);
        $receipt->setTaxation('osn');

        $order = Order::build($orderId, $amount)
            ->withDescription('Тестовая карта: ' . $cardData['description'])
            ->withLanguage(LanguageEnum::RUSSIAN)
            ->withReceipt($receipt);

        $payment = $this->tinkoffClient->initiatePayment($order);

        $this->assertNotNull($payment->getPaymentId());
        $this->assertNotEmpty($payment->getPaymentURL());
        $this->assertTrue($payment->isSuccess());

        echo "\n";
        echo "Payment URL: " . $payment->getPaymentURL() . "\n";
        echo "Payment ID: " . $payment->getPaymentId() . "\n";
        echo "Card PAN: " . $cardData['pan'] . "\n";
        echo "Card Exp Date: " . $cardData['exp_date'] . "\n";
        echo "Card CVV: " . $cardData['cvv'] . "\n";
        echo "Expected result: " . $cardData['description'] . "\n";
        if (isset($cardData['otp_password'])) {
            echo "OTP Password (if requested): " . $cardData['otp_password'] . "\n";
        }
    }

    #[Test]
    public function it_can_get_payment_status(): void
    {
        $orderId = new OrderId('test_' . uniqid());
        $amount = new Amount(10000);

        $customer = new Customer();
        $customer->withEmail($this->email);

        $receipt = new Receipt();
        $receipt->addItem([
            'Name' => 'Тестовый товар',
            'Price' => 10000,
            'Quantity' => 1.00,
            'Amount' => 10000,
            'Tax' => 'vat20',
            'PaymentMethod' => 'full_payment',
            'PaymentObject' => 'commodity'
        ]);
        $receipt->setCustomer($customer);
        $receipt->setTaxation('osn');

        $order = Order::build($orderId, $amount)
            ->withDescription('Тестовый заказ для проверки статуса')
            ->withLanguage(LanguageEnum::RUSSIAN)
            ->withReceipt($receipt);

        $payment = $this->tinkoffClient->initiatePayment($order);
        $paymentStatus = $this->tinkoffClient->getPaymentStatus($payment->getPaymentId());

        $this->assertTrue($paymentStatus->isSuccess());

        $this->assertEquals('NEW', $paymentStatus->getStatus());
    }

    public static function successfulPaymentCardsProvider(): array
    {
        return [
            'successful_3ds2_frictionless' => [TestCardProvider::getSuccessful3ds2FrictionlessCard()],
            'successful_non_3ds'           => [TestCardProvider::getSuccessfulNon3dsCard()],
            'mock_successful'              => [TestCardProvider::getMockSuccessfulCard()]
        ];
    }

    public function unsuccessfulPaymentCardsProvider(): array
    {
        return [
            'error_payment'          => [TestCardProvider::getErrorPaymentCard()],
            'insufficient_funds'     => [TestCardProvider::getInsufficientFundsCard()],
            'restricted_3ds2'        => [TestCardProvider::getRestricted3ds2Card()],
            'not_authenticated_3ds2' => [TestCardProvider::getNotAuthenticated3ds2Card()]
        ];
    }
}