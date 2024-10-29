<?php

declare(strict_types=1);

namespace Src\Domain;

class Email
{
    private string $value;

    public function __construct(public readonly string $email)
    {
        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $this->value = $this->email;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
