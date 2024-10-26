<?php

declare(strict_types=1);

namespace Src;

class DeleteAllReservations
{
    public function __construct(public readonly ReservationRepository $reservationRepository)
    {
    }

    public function execute(): void
    {
        $this->reservationRepository->deleteAllReservations();
    }
}
