<?php

namespace Infrastructure\EventSourcing;

use EventSourcing\Messaging\Event;
use EventSourcing\Messaging\EventBus;
use SimpleBus\Message\Bus\MessageBus;

class SimpleBusEventBus implements EventBus
{
    private $messageBus;

    public function __construct(MessageBus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function handle(Event $event)
    {
        $this->messageBus->handle($event);
    }
}