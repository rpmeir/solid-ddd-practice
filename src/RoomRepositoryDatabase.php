<?php

declare(strict_types=1);

namespace Src;

class RoomRepositoryDatabase implements RoomRepository
{
    const CONNECTION_STRING = 'pgsql:host=127.0.0.1;port=5432;dbname=postgres';
    const PASSWORD = '123456';

    public function getRoom(string $roomId): Room
    {
        $connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
        $statement = $connection->prepare('SELECT * FROM sdp.rooms WHERE room_id = ?');
        $statement->execute([$roomId]);
        [$room] = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return new Room($room['room_id'], $room['type'], (float) $room['price']);
    }
}
