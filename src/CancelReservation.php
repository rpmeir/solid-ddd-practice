<?php

declare(strict_types=1);

namespace Src;

class CancelReservation
{
    public function __construct(public readonly ReservationRepository $reservationRepository)
    {
    }

    public function execute(string $reservationId)
    {
        $reservation = $this->reservationRepository->getReservationById($reservationId);
        $reservation->cancel();
        $this->reservationRepository->updateReservation($reservation);
    }
}
