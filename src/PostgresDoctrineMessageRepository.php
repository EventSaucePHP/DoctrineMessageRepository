<?php

namespace EventSauce\DoctrineMessageRepository;

use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use const JSON_PRETTY_PRINT;
use function join;
use function json_decode;
use function json_encode;
use function reset;

class PostgresDoctrineMessageRepository implements MessageRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var MessageDispatcher
     */
    private $dispatcher;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(Connection $connection, MessageDispatcher $dispatcher, MessageSerializer $serializer, string $tableName)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
        $this->tableName = $tableName;
    }

    public function persist(Message ... $messages)
    {
        if (count($messages) === 0) {
            return;
        }

        $sql = "INSERT INTO {$this->tableName} (event_id, event_type, aggregate_root_id, time_of_recording, payload) VALUES ";
        $params = ['aggregate_root_id' => reset($messages)->aggregateRootId()->toString()];
        $values = [];

        foreach ($messages as $index => $message) {
            $payload = $this->serializer->serializeMessage($message);
            $eventIdColumn = 'event_id_' . $index;
            $eventTypeColumn = 'event_type_' . $index;
            $timeOfRecordingColumn = 'time_of_recording_' . $index;
            $payloadColumn = 'payload_' . $index;
            $values[] = "(:{$eventIdColumn}, :{$eventTypeColumn}, :aggregate_root_id, :{$timeOfRecordingColumn}, :{$payloadColumn})";
            $params[$timeOfRecordingColumn] = $payload['timeOfRecording'];
            $params[$payloadColumn] = json_encode($payload, JSON_PRETTY_PRINT);
            $params[$eventTypeColumn] = $payload['type'];
            $params[$eventIdColumn] = $payload['metadata']['event_id'];
        }

        $sql .= join(', ', $values);
        $this->connection->beginTransaction();
        $this->connection->prepare($sql)->execute($params);
        $this->connection->commit();
        $this->dispatcher->dispatch(...$messages);
    }

    public function retrieveAll(AggregateRootId $id): Generator
    {
        $sql = "SELECT payload FROM {$this->tableName} WHERE aggregate_root_id = :aggregate_root_id ORDER BY time_of_recording ASC";
        $stm = $this->connection->prepare($sql);
        $stm->bindValue('aggregate_root_id', $id->toString());
        $stm->execute();

        while ($payload = $stm->fetchColumn()) {
            yield from $this->serializer->unserializePayload(json_decode($payload, true));
        }
    }
}