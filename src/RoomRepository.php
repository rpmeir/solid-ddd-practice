<?php

declare(strict_types=1);

namespace Src;

interface RoomRepository
{
    public function getRoom(string $roomId): Room;
}
