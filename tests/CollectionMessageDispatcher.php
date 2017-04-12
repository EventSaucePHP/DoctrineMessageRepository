<?php

namespace EventSauce\DoctrineMessageRepository\Tests;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;

class CollectionMessageDispatcher implements MessageDispatcher
{
    public $messages = [];

    public function dispatch(Message ... $messages)
    {
        $this->messages = $messages;
    }
}