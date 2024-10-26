<?php

declare(strict_types=1);

namespace Src;

class MakeReservation
{
    public function __construct(
        public readonly ReservationRepository $reservationRepository,
        public readonly RoomRepository $roomRepository)
    {
    }

    public function execute(object $input): object
    {
        if (!filter_var($input->email, FILTER_VALIDATE_EMAIL)) { throw new \InvalidArgumentException('Invalid email'); }
        $room = $this->roomRepository->getRoom($input->roomId);
        $reservation = ReservationFactory::create($room->type, $room->roomId, $input->email, new \DateTimeImmutable($input->checkinDate), new \DateTimeImmutable($input->checkoutDate));
        $hasActiveReservations = $this->reservationRepository->hasActiveReservations($input->roomId, $input->checkinDate, $input->checkoutDate);
        if ($hasActiveReservations) { throw new \InvalidArgumentException('Room is not available'); }
        $reservation->calculate($room);
        $this->reservationRepository->saveReservation($reservation);
        return (object) ['reservationId' => $reservation->reservationId];
    }
}
