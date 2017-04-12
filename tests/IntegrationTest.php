<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use Doctrine\DBAL\Connection;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\Time\TestClock;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;

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
        $repository = new DoctrineMessageRepository($connection, $dispatcher, new ConstructingMessageSerializer(), 'domain_messages');
        $aggregateRootId = AggregateRootId::create();

        $repository->persist();
        $this->assertEmpty(iterator_to_array($repository->retrieveAll($aggregateRootId)));

        $message = new Message(new TestEvent($aggregateRootId, (new TestClock())->pointInTime()));
        $repository->persist($message);
        $retrievedMessage = iterator_to_array($repository->retrieveAll($aggregateRootId))[0];
        $this->assertEquals($message, $retrievedMessage);
        $this->assertEquals($message, $dispatcher->messages[0]);
    }
}