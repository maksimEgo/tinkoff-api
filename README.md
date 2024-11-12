> # Tinkoff API Integration
> 
> This project is a PHP implementation for integrating with Tinkoff's payment gateway using a Domain-Driven Design (DDD) and SOLID principles. The package allows easy integration of Tinkoff's payment services into any PHP application.
> 
> ## Features
> - Create a payment (`InitPayment`) through the Tinkoff API.
> - Type-safe models using value objects for `Amount`, `OrderId`, etc.
> - Mappers for converting API responses to corresponding entities.
> - Abstract payment class to handle common payment attributes.
> 
> ## Project Structure
> 
> - **src/Domain**: Contains the core domain entities and value objects.
>   - **Entity**: Contains abstract and concrete payment entities, such as `Payment` and `InitPayment`.
>   - **Enum**: Enumeration classes like `LanguageEnum` and `PayTypeEnum`.
>   - **ValueObject**: Value objects like `OrderId` and `Amount`.
> 
> - **src/Infrastructure**: Contains infrastructure code for API communication.
>   - **Mapper**: Handles mapping between arrays and domain entities.
>   - **TinkoffClient**: Implements the communication with the Tinkoff API.
> 
> ## Installation
> 
> To install the package, navigate to your project directory and run:
> 
> ```sh
> composer require egorov/tinkoff-api
> ```
> 
> ## Usage
> 
> ### Creating a Payment
> 
> To create a new payment, you need to create an instance of the `Order` entity and use `TinkoffClient` to initiate the payment:
> 
> ```php
> require __DIR__ . '/../vendor/autoload.php';
> 
> use Egorov\TinkoffApi\Domain\Entity\Order;
> use Egorov\TinkoffApi\Domain\ValueObject\Amount;
> use Egorov\TinkoffApi\Domain\ValueObject\OrderId;
> use Egorov\TinkoffApi\Infrastructure\TinkoffClient;
> 
> $orderId = new OrderId('12345');
> $amount = new Amount(10000);
> 
> $order = Order::build($orderId, $amount)
>     ->withDescription('Payment for Order #12345');
> 
> $terminalKey = 'YourTerminalKey';
> $password = 'YourPassword';
> 
> $tinkoffClient = new TinkoffClient($terminalKey, $password);
> $payment = $tinkoffClient->initiatePayment($order);
> 
> if ($payment->isSuccess()) {
>     echo "Payment successfully initiated. Payment URL: " . $payment->getPaymentUrl();
> } else {
>     echo "Failed to initiate payment. Error Code: " . $payment->getErrorCode();
> }
> ```
> 
> ### Mapping Response to Payment Entity
> 
> The API responses are mapped to specific payment entities like `InitPayment`. This allows for type-safe interaction with the data:
> 
> ```php
> use Egorov\TinkoffApi\Infrastructure\Mapper\InitPaymentMapper;
> 
> $responseData = [
>     'OrderId' => 'Order123',
>     'Amount' => 10000,
>     'Status' => 'NEW',
>     'TerminalKey' => 'TinkoffBankTest',
>     'PaymentURL' => 'https://securepayments.tinkoff.ru/dWCdXdBf',
> ];
> 
> $initPayment = InitPaymentMapper::fromArray($responseData);
> 
> echo "Payment Status: " . $initPayment->getStatus() . "\n";
> echo "Payment URL: " . $initPayment->getPaymentUrl() . "\n";
> ```
> 
> ## Project Principles
> 
> - **Domain-Driven Design (DDD)**: The project follows DDD principles by clearly separating domain logic from infrastructure and application logic.
> - **SOLID Principles**: Each class follows single responsibility and open/closed principles, making the codebase easier to extend and maintain.
> 
> ## Contributing
> 
> Feel free to submit issues or pull requests if you encounter bugs or have suggestions for improvements.
> 
> ## License
> 
> This project is licensed under the MIT License.

