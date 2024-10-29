<?php

declare(strict_types=1);

namespace Src\Domain;

abstract class Reservation
{
    protected Period $period;
    private Email $email;

    public function __construct(
        public readonly string $reservationId,
        public readonly string $roomId,
        public string $emailString,
        public readonly \DateTimeImmutable $checkinDate,
        public readonly \DateTimeImmutable $checkoutDate,
        private string $status,
        protected float $price = 0,
        protected int $duration = 0
    ) {
        $this->email = new Email($emailString);
        $this->period = new Period($checkinDate, $checkoutDate);
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

    public function getEmail(): string
    {
        return $this->email->getValue();
    }

    abstract public function calculate(Room $room): void;
}
