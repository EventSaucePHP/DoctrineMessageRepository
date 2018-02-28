<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use EventSauce\DoctrineMessageRepository\BaseDoctrineMessageRepository;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\Time\TestClock;
use EventSauce\EventSourcing\UuidAggregateRootId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function iterator_to_array;

abstract class DoctrineIntegrationTestCase extends TestCase
{
    abstract protected function connection(): Connection;

    abstract protected function messageRepository(
        Connection $connection,
        MessageSerializer $serializer,
        string $tableName
    ): BaseDoctrineMessageRepository;

    /**
     * @test
     */
    public function it_works()
    {
        $connection = $this->doctrineConnection();
        $serializer = new ConstructingMessageSerializer();
        $repository = $this->messageRepository($connection, $serializer, 'domain_messages');
        $aggregateRootId = UuidAggregateRootId::create();
        $repository->persist();
        $this->assertEmpty(iterator_to_array($repository->retrieveAll($aggregateRootId)));

        $eventId = Uuid::uuid4()->toString();
        $message = new Message(new TestEvent((new TestClock())->pointInTime()), [
            'event_id'          => $eventId,
            'aggregate_root_id' => $aggregateRootId->toString(),
        ]);
        $repository->persist($message);
        $retrievedMessage = iterator_to_array($repository->retrieveAll($aggregateRootId), false)[0];
        $this->assertEquals($message, $retrievedMessage);
    }

    /**
     * @test
     */
    public function persisting_events_without_aggregate_root_ids()
    {
        $connection = $this->doctrineConnection();
        $serializer = new ConstructingMessageSerializer();
        $repository = $this->messageRepository($connection, $serializer, 'domain_messages');
        $eventId = Uuid::uuid4();
        $message = new Message(new TestEvent((new TestClock())->pointInTime()), [
            'event_id' => $eventId->toString(),
        ]);
        $repository->persist($message);
        $persistedMessages = iterator_to_array($repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertEquals($message, $persistedMessages[0]);
    }

    /**
     * @test
     */
    public function persisting_events_without_event_ids()
    {
        $connection = $this->doctrineConnection();
        $serializer = new ConstructingMessageSerializer();
        $repository = $this->messageRepository($connection, $serializer, 'domain_messages');
        $message = new Message(new TestEvent((new TestClock())->pointInTime()));
        $repository->persist($message);
        $persistedMessages = iterator_to_array($repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertNotEquals($message, $persistedMessages[0]);
    }

    /**
     * @return Connection
     * @throws DBALException
     */
    private function doctrineConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->connection();
        $connection->exec('TRUNCATE TABLE domain_messages');

        return $connection;
    }
}