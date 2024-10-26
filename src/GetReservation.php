<?php

declare(strict_types=1);

namespace Src;

class GetReservation
{
    public function __construct(public readonly ReservationRepository $reservationRepository)
    {
    }

    public function execute($reservationId): object
    {
        $reservation = $this->reservationRepository->getReservationById($reservationId);
        // returning DTO instead of object because of inaccessible properties (limitations of HTTP client used in tests)
        return (object) [
            'reservationId' => $reservation->reservationId,
            'roomId' => $reservation->roomId,
            'email' => $reservation->email,
            'checkinDate' => $reservation->checkinDate,
            'checkoutDate' => $reservation->checkoutDate,
            'status' => $reservation->getStatus(),
            'price' => $reservation->getPrice(),
            'duration' => $reservation->getDuration(),
        ];
    }
}
