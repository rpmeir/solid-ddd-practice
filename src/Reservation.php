<?php

declare(strict_types=1);

namespace Src;

abstract class Reservation
{
    public function __construct(
        public readonly string $reservationId,
        public readonly string $roomId,
        public readonly string $email,
        public readonly \DateTimeImmutable $checkinDate,
        public readonly \DateTimeImmutable $checkoutDate,
        private readonly string $status,
        protected float $price = 0,
        protected int $duration = 0
    ) {
    }

    public function cancel(): void
    {
        if ($this->status === 'cancelled') {
            throw new \DomainException('Reservation is already cancelled');
        }
        $this->status = 'cancelled';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    abstract public function calculate(Room $room): void;
}
