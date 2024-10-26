<?php

declare(strict_types=1);

namespace Src;

interface ReservationRepository
{
    /**
     * Summary of getActiveReservations
     * @return array<Reservation>
     */
    public function hasActiveReservations(string $roomId, string $checkinDate, string $checkoutDate): bool;
    public function saveReservation(Reservation $reservation): void;
    public function updateReservation(Reservation $reservation): void;
    public function getReservationById(string $reservationId): Reservation;
    public function deleteAllReservations(): void;
}
