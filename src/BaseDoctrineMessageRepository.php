<?php

namespace EventSauce\DoctrineMessageRepository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use function json_decode;

abstract class BaseDoctrineMessageRepository implements MessageRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var MessageSerializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $tableName;

    public function __construct(Connection $connection, MessageSerializer $serializer, string $tableName)
    {
        $this->connection = $connection;
        $this->serializer = $serializer;
        $this->tableName = $tableName;
    }

    public function retrieveAll(AggregateRootId $id): Generator
    {
        /** @var Statement $stm */
        $stm = $this->connection->createQueryBuilder()
            ->select('payload')
            ->from($this->tableName)
            ->where('aggregate_root_id = :aggregate_root_id')
            ->orderBy('time_of_recording', 'ASC')
            ->setParameter('aggregate_root_id', $id->toString())
            ->execute();

        while ($payload = $stm->fetchColumn()) {
            yield from $this->serializer->unserializePayload(json_decode($payload, true));
        }
    }
}