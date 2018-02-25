<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use EventSauce\EventSourcing\Event;
use EventSauce\EventSourcing\PointInTime;

class TestEvent implements Event
{
    /**
     * @var PointInTime
     */
    private $timeOfRecording;

    public function __construct(PointInTime $timeOfRecording)
    {
        $this->timeOfRecording = $timeOfRecording;
    }

    public function timeOfRecording(): PointInTime
    {
        return $this->timeOfRecording;
    }

    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload, PointInTime $timeOfRecording): Event
    {
        return new TestEvent($timeOfRecording);
    }
}