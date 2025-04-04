<?php

namespace Egorov\TinkoffApi\Domain\Entity;

use InvalidArgumentException;

class Customer
{
    private ?string $email = null;

    private ?string $phone = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function withPhone(string $phone): self
    {
        if (!preg_match('/^\+[0-9]+$/', $phone)) {
            throw new InvalidArgumentException('Invalid phone format. Should start with + and contain only digits');
        }

        $this->phone = $phone;
        return $this;
    }

    public function withEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
        return $this;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->email !== null) {
            $result['Email'] = $this->email;
        }

        if ($this->phone !== null) {
            $result['Phone'] = $this->phone;
        }

        return $result;
    }
}