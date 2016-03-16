<?php

namespace Infrastructure\EventSourcing;

use EventSourcing\Event;
use EventSourcing\EventBus;
use SimpleBus\Message\Bus\MessageBus;

class SimpleBusMessageBus implements EventBus
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