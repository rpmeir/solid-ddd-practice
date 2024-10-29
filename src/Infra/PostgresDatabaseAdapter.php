<?php

declare(strict_types=1);

namespace Src\Infra;

class PostgresDatabaseAdapter implements Connection
{
    public const CONNECTION_STRING = 'pgsql:host=127.0.0.1;port=5432;dbname=postgres';
    public const PASSWORD = '123456';
    private ?\PDO $connection = null;

    public function __construct()
    {
        $this->connection = new \PDO(self::CONNECTION_STRING, 'postgres', self::PASSWORD);
    }

    public function query(string $statement, array $params): array
    {
        $statement = $this->connection->prepare($statement);
        $statement->execute($params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function execute(string $statement, ?array $params = null): void
    {
        $statement = $this->connection->prepare($statement);
        $statement->execute($params);
    }
}
