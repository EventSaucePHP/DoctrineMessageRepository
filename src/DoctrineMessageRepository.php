<?php

namespace EventSauce\DoctrineMessageRepository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Result;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use EventSauce\EventSourcing\UnableToRetrieveMessages;
use Generator;
use function is_int;
use function json_decode;
use Ramsey\Uuid\Uuid;

class DoctrineMessageRepository implements MessageRepository
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

    /**
     * @var int
     */
    private $jsonEncodeOptions;

    public function __construct(
        Connection $connection,
        MessageSerializer $serializer,
        string $tableName,
        int $jsonEncodeOptions = 0
    ) {
        $this->connection = $connection;
        $this->serializer = $serializer;
        $this->tableName = $tableName;
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function persist(Message ...$messages): void
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
            $aggregateRootVersionColumn = 'aggregate_root_version_' . $index;
            $payloadColumn = 'payload_' . $index;
            $values[] = "(:{$eventIdColumn}, :{$eventTypeColumn}, :{$aggregateRootIdColumn}, :{$aggregateRootVersionColumn}, :{$payloadColumn})";
            $params[$aggregateRootVersionColumn] = (int) ($payload['headers'][Header::AGGREGATE_ROOT_VERSION] ?? 0);
            $params[$eventIdColumn] = $payload['headers'][Header::EVENT_ID] = $payload['headers'][Header::EVENT_ID] ?? Uuid::uuid4(
                )->toString();
            $params[$payloadColumn] = json_encode($payload, $this->jsonEncodeOptions);
            $params[$eventTypeColumn] = $payload['headers'][Header::EVENT_TYPE] ?? null;
            $params[$aggregateRootIdColumn] = $payload['headers'][Header::AGGREGATE_ROOT_ID] ?? null;
        }

        $sql .= implode(', ', $values);
        $this->connection->beginTransaction();
        $this->connection->prepare($sql)->execute($params);
        $this->connection->commit();
    }

    protected function baseSql(string $tableName): string
    {
        return "INSERT INTO {$tableName} (event_id, event_type, aggregate_root_id, aggregate_root_version, payload) VALUES ";
    }

    /** @psalm-return Generator<Message> */
    public function retrieveAll(AggregateRootId $id): Generator
    {
        $stm = $this->connection->createQueryBuilder()->select('payload')->from($this->tableName)->where(
                'aggregate_root_id = :aggregate_root_id'
            )->orderBy('aggregate_root_version', 'ASC')->setParameter('aggregate_root_id', $id->toString())->execute();

        if (is_int($stm)) {
            throw UnableToRetrieveMessages::dueTo("Received an invalid response from Doctrine DBAL.");
        }

        return $this->yieldMessagesForResult($stm);
    }

    /** @psalm-return Generator<Message> */
    public function retrieveEverything(): Generator
    {
        $stm = $this->connection->createQueryBuilder()->select('payload')->from($this->tableName)->execute();

        if (is_int($stm)) {
            throw UnableToRetrieveMessages::dueTo("Received an invalid response from Doctrine DBAL.");
        }

        return $this->yieldMessagesForResult($stm);
    }

    /** @psalm-return Generator<Message> */
    public function retrieveAllAfterVersion(AggregateRootId $id, int $aggregateRootVersion): Generator
    {
        $stm = $this->connection->createQueryBuilder()->select('payload')->from($this->tableName)->where(
                'aggregate_root_id = :aggregate_root_id'
            )->andWhere('aggregate_root_version > :aggregate_root_version')->orderBy(
                'aggregate_root_version',
                'ASC'
            )->setParameter('aggregate_root_id', $id->toString())->setParameter(
                'aggregate_root_version',
                $aggregateRootVersion
            )->execute();

        if (is_int($stm)) {
            throw UnableToRetrieveMessages::dueTo("Received an invalid response from Doctrine DBAL.");
        }

        return $this->yieldMessagesForResult($stm);
    }

    /**
     * @psalm-return Generator<Message>
     * @psalm-suppress MissingParamType the return interface was renamed, which makes it impossible to add typing for
     *                 both. The solution here is to omit type information so the responses from both major versions
     *                 of Doctrine are supported.
     */
    private function yieldMessagesForResult($stm): Generator
    {
        $message = null;

        while ($payload = $stm->fetchAssociative()) {
            /** @var array<string, mixed> $payload */
            $payload = json_decode($payload['payload'], true);
            $message = $this->serializer->unserializePayload($payload);
            yield $message;
        }

        return $message instanceof Message ? $message->header(Header::AGGREGATE_ROOT_VERSION) ?: 0 : 0;
    }
}
