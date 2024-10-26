<?php

declare(strict_types=1);

namespace Src;

class GetReservation
{
    public function __construct(public readonly ReservationRepository $reservationRepository)
    {
    }

    public function execute($reservationId): Reservation
    {
        return $this->reservationRepository->getReservationById($reservationId);
    }
}
