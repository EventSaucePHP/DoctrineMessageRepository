<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use EventSauce\EventSourcing\Serialization\SerializableEvent;

class TestEvent implements SerializableEvent
{
    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new TestEvent();
    }
}