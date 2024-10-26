<?php

declare(strict_types=1);

namespace Src;

use Ramsey\Uuid\Uuid;

class HourReservation extends Reservation
{
    public function calculate(Room $room): void
    {
        $this->duration = $this->checkinDate->diff($this->checkoutDate)->h;
        $this->price = $this->duration * $room->price;
    }

    public static function create(string $roomId, string $email, \DateTimeImmutable $checkinDate, \DateTimeImmutable $checkoutDate): self
    {
        $reservationId = Uuid::uuid4()->toString();
        $status = 'active';
        return new self($reservationId, $roomId, $email, $checkinDate, $checkoutDate, $status);
    }
}
