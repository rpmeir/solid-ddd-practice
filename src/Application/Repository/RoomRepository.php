<?php

declare(strict_types=1);

namespace Src\Application\Repository;

use Src\Domain\Room;

interface RoomRepository
{
    public function getRoom(string $roomId): Room;
}
