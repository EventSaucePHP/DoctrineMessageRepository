<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\PostgresDoctrineMessageRepository;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

class PostgresIntegrationTest extends DoctrineIntegrationTestCase
{
    protected function connection(): Connection
    {
        return require __DIR__ . '/postgres-connection.php';
    }

    protected function messageRepository(
        Connection $connection,
        MessageDispatcher $dispatcher,
        MessageSerializer $serializer,
        string $tableName
    ): MessageRepository {
        return new PostgresDoctrineMessageRepository($connection, $dispatcher, $serializer, $tableName);
    }
}