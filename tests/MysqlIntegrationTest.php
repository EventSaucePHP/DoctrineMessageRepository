<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\BaseDoctrineMessageRepository;
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
        MessageSerializer $serializer,
        string $tableName
    ): BaseDoctrineMessageRepository {
        return new MysqlDoctrineMessageRepository($connection, $serializer, $tableName);
    }
}