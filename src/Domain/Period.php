<?php

declare(strict_types=1);

namespace Src\Domain;

class Period
{
    public function __construct(public readonly \DateTimeImmutable $start, public readonly \DateTimeImmutable $end)
    {
        if ($this->start > $this->end) {
            throw new \InvalidArgumentException('Invalid period');
        }
    }

    public function getDiffInDays(): int
    {
        return $this->end->diff($this->start)->days;
    }

    public function getDiffInHours(): int
    {
        return $this->end->diff($this->start)->h;
    }
}
