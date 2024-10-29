<?php

declare(strict_types=1);

namespace Src\Infra;

interface Connection
{
    public function query(string $statement, array $params): array;
    public function execute(string $statement, ?array $params = null): void;
}
