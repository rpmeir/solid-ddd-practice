<?php

declare(strict_types=1);

namespace Src;

class ReservationRepositoryDatabase implements ReservationRepository
{
    const CONNECTION_STRING = 'pgsql:host=127.0.0.1;port=5432;dbname=postgres';
    const PASSWORD = '123456';

    public function hasActiveReservations(string $roomId, string $checkinDate, string $checkoutDate): bool
    {
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $reservations = $connection->prepare("SELECT * FROM sdp.reservations WHERE room_id = ? AND (checkin_date, checkout_date) OVERLAPS (?, ?) AND status = 'active'");
        $reservations->execute([$roomId, $checkinDate, $checkoutDate]);
        $reservations = $reservations->fetchAll(\PDO::FETCH_ASSOC);
        return count($reservations) > 0;
    }

    public function saveReservation(object $reservation): void
    {
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $statement = $connection->prepare("INSERT INTO sdp.reservations (reservation_id, room_id, email, checkin_date, checkout_date, price, status, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute([$reservation->reservationId, $reservation->roomId, $reservation->email, $reservation->checkinDate->format('Y-m-d H:i:s'), $reservation->checkoutDate->format('Y-m-d H:i:s'), $reservation->getPrice(), $reservation->getStatus(), $reservation->getDuration()]);
    }

    public function updateReservation(Reservation $reservation): void
    {
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $statement = $connection->prepare("UPDATE sdp.reservations SET status = ? WHERE reservation_id = ?");
        $statement->execute([$reservation->getStatus(), $reservation->reservationId]);
    }

    public function getReservationById(string $reservationId): Reservation
    {
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $statement = $connection->prepare("SELECT r.*, o.type FROM sdp.reservations r JOIN sdp.rooms o USING (room_id) WHERE reservation_id = ?");
        $statement->execute([$reservationId]);
        [$reservation] = $statement->fetchAll(\PDO::FETCH_ASSOC);
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
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $statement = $connection->prepare("DELETE FROM sdp.reservations");
        $statement->execute();
    }
}
