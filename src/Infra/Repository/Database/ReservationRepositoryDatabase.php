<?php

declare(strict_types=1);

namespace Src\Infra\Repository\Database;

use Src\Application\Repository\ReservationRepository;
use Src\Domain\Reservation;
use Src\Domain\ReservationFactory;
use Src\Infra\Connection;

class ReservationRepositoryDatabase implements ReservationRepository
{
    public function __construct(public readonly Connection $connection)
    {
    }

    public function hasActiveReservations(string $roomId, string $checkinDate, string $checkoutDate): bool
    {
        $reservations = $this->connection->query(
            "SELECT * FROM sdp.reservations WHERE room_id = ? AND (checkin_date, checkout_date) OVERLAPS (?, ?) AND status = 'active'",
            [$roomId, $checkinDate, $checkoutDate]
        );
        return count($reservations) > 0;
    }

    public function saveReservation(Reservation $reservation): void
    {
        $this->connection->execute(
            'INSERT INTO sdp.reservations (reservation_id, room_id, email, checkin_date, checkout_date, price, status, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$reservation->reservationId, $reservation->roomId, $reservation->getEmail(), $reservation->checkinDate->format('Y-m-d H:i:s'), $reservation->checkoutDate->format('Y-m-d H:i:s'), $reservation->getPrice(), $reservation->getStatus(), $reservation->getDuration()]
        );
    }

    public function updateReservation(Reservation $reservation): void
    {
        $this->connection->execute(
            'UPDATE sdp.reservations SET status = ? WHERE reservation_id = ?',
            [$reservation->getStatus(), $reservation->reservationId]
        );
    }

    public function getReservationById(string $reservationId): Reservation
    {
        [$reservation] = $this->connection->query(
            'SELECT r.*, o.type FROM sdp.reservations r JOIN sdp.rooms o USING (room_id) WHERE reservation_id = ?',
            [$reservationId]
        );
        $reservationDTO = [
            'reservationId' => $reservation['reservation_id'],
            'roomId' => $reservation['room_id'],
            'email' => $reservation['email'],
            'checkinDate' => new \DateTimeImmutable($reservation['checkin_date']),
            'checkoutDate' => new \DateTimeImmutable($reservation['checkout_date']),
            'status' => $reservation['status'],
            'price' => (float) $reservation['price'],
            'duration' => (int) $reservation['duration'],
            'type' => $reservation['type'],
        ];
        return ReservationFactory::restore($reservationDTO);
    }

    public function deleteAllReservations(): void
    {
        $this->connection->execute('DELETE FROM sdp.reservations');
    }
}
