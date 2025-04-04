<?php

declare(strict_types=1);

namespace Egorov\TinkoffApi\Domain\Token;

abstract class AbstractTokenGenerator implements TokenGeneratorInterface
{
    protected string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function generate(array $data): string
    {
        $filteredData = array_filter($data, function ($value, $key) {
            return $value !== null && $value !== '' && $key !== 'Token' && $key !== 'Receipt';
        }, ARRAY_FILTER_USE_BOTH);

        $filteredData['Password'] = $this->password;

        ksort($filteredData);

        $debug = [];
        foreach ($filteredData as $key => $value) {
            $debug[] = "$key: $value";
        }
        error_log("Sorted parameters: " . implode(', ', $debug));

        $valueString = '';
        foreach ($filteredData as $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            if (is_array($value)) {
                continue;
            }

            if (is_object($value) && method_exists($value, '__toString')) {
                $value = (string)$value;
            }

            $valueString .= $value;
        }

        error_log("Final token string: " . $valueString);

        return hash('sha256', $valueString);
    }
}