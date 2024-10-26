<?php

declare(strict_types=1);

namespace Src;

class Room
{
    public function __construct(
        public readonly string $roomId,
        public readonly string $type,
        public readonly float $price
    ) {
    }
}
