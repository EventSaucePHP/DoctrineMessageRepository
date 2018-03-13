<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\Time\Clock;
use EventSauce\EventSourcing\Time\TestClock;
use EventSauce\EventSourcing\UuidAggregateRootId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function iterator_to_array;

abstract class DoctrineIntegrationTestCase extends TestCase
{
    /**
     * @var DoctrineMessageRepository
     */
    private $repository;

    /**
     * @var DefaultHeadersDecorator
     */
    private $decorator;

    /**
     * @var Clock
     */
    private $clock;

    abstract protected function connection(): Connection;

    abstract protected function messageRepository(
        Connection $connection,
        MessageSerializer $serializer,
        string $tableName
    ): DoctrineMessageRepository;

    protected function setUp()
    {
        parent::setUp();
        $connection = $this->connection();
        $connection->exec('TRUNCATE TABLE domain_messages');
        $serializer = new ConstructingMessageSerializer();
        $this->clock = new TestClock();
        $this->decorator = new DefaultHeadersDecorator(null, $this->clock);
        $this->repository = $this->messageRepository($connection, $serializer, 'domain_messages');
    } 

    /**
     * @test
     */
    public function it_works()
    {
        $aggregateRootId = UuidAggregateRootId::create();
        $this->repository->persist();
        $this->assertEmpty(iterator_to_array($this->repository->retrieveAll($aggregateRootId)));

        $eventId = Uuid::uuid4()->toString();
        $message = $this->decorator->decorate(new Message(new TestEvent(), [
            Header::EVENT_ID          => $eventId,
            Header::AGGREGATE_ROOT_ID => $aggregateRootId->toString(),
        ]));
        $this->repository->persist($message);
        $retrievedMessage = iterator_to_array($this->repository->retrieveAll($aggregateRootId), false)[0];
        $this->assertEquals($message, $retrievedMessage);
    }

    /**
     * @test
     */
    public function persisting_events_without_aggregate_root_ids()
    {
        $eventId = Uuid::uuid4();
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->pointInTime()), [
            Header::EVENT_ID => $eventId->toString(),
        ]));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertEquals($message, $persistedMessages[0]);
    }

    /**
     * @test
     */
    public function persisting_events_without_event_ids()
    {
        $message = $this->decorator->decorate(new Message(new TestEvent((new TestClock())->pointInTime())));
        $this->repository->persist($message);
        $persistedMessages = iterator_to_array($this->repository->retrieveEverything());
        $this->assertCount(1, $persistedMessages);
        $this->assertNotEquals($message, $persistedMessages[0]);
    }
}