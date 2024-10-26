<?php

declare(strict_types=1);

namespace Src;

use Ramsey\Uuid\Uuid;

class DayReservation extends Reservation
{
    public function calculate(Room $room): void
    {
        $this->duration = $this->checkinDate->diff($this->checkoutDate)->days;
        $this->price = $this->duration * $room->price;
    }

    public static function create(string $roomId, string $email, \DateTimeImmutable $checkinDate, \DateTimeImmutable $checkoutDate): self
    {
        $reservationId = Uuid::uuid4()->toString();
        $status = 'active';
        return new self($reservationId, $roomId, $email, $checkinDate, $checkoutDate, $status);
    }
}
