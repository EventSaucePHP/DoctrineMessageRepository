<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

class TestEvent implements SerializablePayload
{
    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new TestEvent();
    }
}
