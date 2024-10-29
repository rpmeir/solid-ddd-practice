<?php

declare(strict_types=1);

namespace Src\Application\Usecase;

use Src\Application\Repository\ReservationRepository;

class CancelReservation
{
    public function __construct(public readonly ReservationRepository $reservationRepository)
    {
    }

    public function execute(string $reservationId): void
    {
        $reservation = $this->reservationRepository->getReservationById($reservationId);
        $reservation->cancel();
        $this->reservationRepository->updateReservation($reservation);
    }
}
