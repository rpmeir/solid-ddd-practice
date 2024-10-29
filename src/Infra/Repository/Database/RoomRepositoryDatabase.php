<?php

declare(strict_types=1);

namespace Src\Infra\Repository\Database;

use Src\Application\Repository\RoomRepository;
use Src\Domain\Room;
use Src\Infra\Connection;

class RoomRepositoryDatabase implements RoomRepository
{
    public function __construct(public readonly Connection $connection)
    {
    }

    public function getRoom(string $roomId): Room
    {
        [$room] = $this->connection->query('SELECT * FROM sdp.rooms WHERE room_id = ?', [$roomId]);
        return new Room($room['room_id'], $room['type'], (float) $room['price']);
    }
}
