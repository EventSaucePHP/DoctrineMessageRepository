<?php

namespace EventSauce\DoctrineMessageRepository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use function json_decode;
use Ramsey\Uuid\Uuid;

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

    public function persist(Message ... $messages)
    {
        if (count($messages) === 0) {
            return;
        }

        $sql = $this->baseSql($this->tableName);
        $params = [];
        $values = [];

        foreach ($messages as $index => $message) {
            $payload = $this->serializer->serializeMessage($message);
            $eventIdColumn = 'event_id_' . $index;
            $aggregateRootIdColumn = 'aggregate_root_id_' . $index;
            $eventTypeColumn = 'event_type_' . $index;
            $timeOfRecordingColumn = 'time_of_recording_' . $index;
            $payloadColumn = 'payload_' . $index;
            $values[] = "(:{$eventIdColumn}, :{$eventTypeColumn}, :{$aggregateRootIdColumn}, :{$timeOfRecordingColumn}, :{$payloadColumn})";
            $params[$timeOfRecordingColumn] = $payload['headers'][Header::TIME_OF_RECORDING];
            $params[$eventIdColumn] = $payload['headers'][Header::EVENT_ID] = $payload['headers'][Header::EVENT_ID] ?? Uuid::uuid4()->toString();
            $params[$payloadColumn] = json_encode($payload, JSON_PRETTY_PRINT);
            $params[$eventTypeColumn] = $payload['headers'][Header::EVENT_TYPE];
            $params[$aggregateRootIdColumn] = $payload['headers'][Header::AGGREGATE_ROOT_ID] ?? null;
        }

        $sql .= join(', ', $values);
        $this->connection->beginTransaction();
        $this->connection->prepare($sql)->execute($params);
        $this->connection->commit();
    }

    abstract protected function baseSql(string $tableName): string;

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

    public function retrieveEverything(): Generator
    {
        /** @var Statement $stm */
        $stm = $this->connection->createQueryBuilder()
            ->select('payload')
            ->from($this->tableName)
            ->orderBy('time_of_recording', 'ASC')
            ->execute();

        while ($payload = $stm->fetchColumn()) {
            yield from $this->serializer->unserializePayload(json_decode($payload, true));
        }
    }
}