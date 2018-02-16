<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\UuidAggregateRootId;
use EventSauce\EventSourcing\Time\TestClock;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class IntegrationTest extends TestCase
{
    /**
     * @test
     */
    public function it_works()
    {
        /** @var Connection $connection */
        $connection = include __DIR__.'/connection.php';
        $connection->exec('TRUNCATE TABLE domain_messages');
        $dispatcher = new CollectionMessageDispatcher();
        $serializer = new ConstructingMessageSerializer(UuidAggregateRootId::class);
        $repository = new DoctrineMessageRepository($connection, $dispatcher, $serializer, 'domain_messages');
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