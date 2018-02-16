<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\MysqlDoctrineMessageRepository;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;

class MysqlIntegrationTest extends DoctrineIntegrationTestCase
{
    protected function connection(): Connection
    {
        return require __DIR__.'/mysql-connection.php';
    }

    protected function messageRepository(
        Connection $connection,
        MessageDispatcher $dispatcher,
        MessageSerializer $serializer,
        string $tableName
    ): MessageRepository {
        return new MysqlDoctrineMessageRepository($connection, $dispatcher, $serializer, $tableName);
    }
}