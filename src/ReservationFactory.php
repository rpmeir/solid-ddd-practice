<?php

declare(strict_types=1);

namespace Src;

class ReservationFactory
{
    public static function create(string $type, string $roomId, string $email, \DateTimeImmutable $checkinDate, \DateTimeImmutable $checkoutDate): Reservation
    {
        if ($type === 'day') {
            return DayReservation::create($roomId, $email, $checkinDate, $checkoutDate);
        }
        if ($type === 'hour') {
            return HourReservation::create($roomId, $email, $checkinDate, $checkoutDate);
        }
        throw new \InvalidArgumentException('Invalid room type');
    }

    public static function restore(array $reservation): Reservation
    {
        if ($reservation['type'] === 'day') {
            return new DayReservation($reservation['reservationId'], $reservation['roomId'], $reservation['email'], $reservation['checkinDate'], $reservation['checkoutDate'], $reservation['status'], $reservation['price'], $reservation['duration']);
        }
        if ($reservation['type'] === 'hour') {
            return new HourReservation($reservation['reservationId'], $reservation['roomId'], $reservation['email'], $reservation['checkinDate'], $reservation['checkoutDate'], $reservation['status'], $reservation['price'], $reservation['duration']);
        }
        throw new \InvalidArgumentException('Invalid room type');
    }
}
