<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\DoctrineMessageRepository\PostgresDoctrineMessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

class PostgresIntegrationTest extends DoctrineIntegrationTestCase
{
    protected function connection(): Connection
    {
        return require __DIR__ . '/postgres-connection.php';
    }

    protected function messageRepository(
        Connection $connection,
        MessageSerializer $serializer,
        string $tableName
    ): DoctrineMessageRepository {
        return new PostgresDoctrineMessageRepository($connection, $serializer, $tableName);
    }
}