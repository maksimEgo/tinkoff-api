<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Token;

abstract class AbstractTokenGenerator implements TokenGeneratorInterface
{
    protected string $password;
    protected array $requiredKeys = [];

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function generate(array $data): string
    {
        $filteredData = $this->filterData($data);
        $filteredData['Password'] = $this->password;

        ksort($filteredData);
        $values = array_values($filteredData);
        return hash('sha256', implode('', $values));
    }

    protected function filterData(array $data): array
    {
        return array_filter(
            $data,
            fn($key) => in_array($key, $this->requiredKeys, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}
