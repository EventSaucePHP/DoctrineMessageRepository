<?php

namespace EventSauce\DoctrineMessageRepository;

use EventSauce\EventSourcing\Message;
use const JSON_PRETTY_PRINT;
use function join;
use function json_encode;
use function reset;

class PostgresDoctrineMessageRepository extends BaseDoctrineMessageRepository
{
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
}