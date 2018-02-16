<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\MysqlDoctrineMessageRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\Time\TestClock;
use EventSauce\EventSourcing\UuidAggregateRootId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function iterator_to_array;
use function var_dump;

abstract class DoctrineIntegrationTestCase extends TestCase
{
    abstract protected function connection(): Connection;

    abstract protected function messageRepository(
        Connection $connection,
        MessageDispatcher $dispatcher,
        MessageSerializer $serializer,
        string $tableName
    ): MessageRepository;

    /**
     * @test
     */
    public function it_works()
    {
        /** @var Connection $connection */
        $connection = $this->connection();
        $connection->exec('TRUNCATE TABLE domain_messages');
        $dispatcher = new CollectionMessageDispatcher();
        $serializer = new ConstructingMessageSerializer(UuidAggregateRootId::class);
        $repository = $this->messageRepository($connection, $dispatcher, $serializer, 'domain_messages');
        $aggregateRootId = UuidAggregateRootId::create();

        $repository->persist();
        $this->assertEmpty(iterator_to_array($repository->retrieveAll($aggregateRootId)));

        $eventId = Uuid::uuid4()->toString();
        $message = new Message(new TestEvent($aggregateRootId, (new TestClock())->pointInTime()), ['event_id' => $eventId]);
        $repository->persist($message);
        $retrievedMessage = iterator_to_array($repository->retrieveAll($aggregateRootId), false)[0];
        $this->assertEquals($message, $retrievedMessage);
        $this->assertEquals($message, $dispatcher->messages[0]);
    }
}