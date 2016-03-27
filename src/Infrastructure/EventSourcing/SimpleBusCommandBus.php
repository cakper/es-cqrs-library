<?php

namespace Infrastructure\EventSourcing;

use EventSourcing\Messaging\Command;
use EventSourcing\Messaging\CommandBus;
use SimpleBus\Message\Bus\MessageBus;

class SimpleBusCommandBus implements CommandBus
{
    private $messageBus;

    public function __construct(MessageBus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function handle(Command $event)
    {
        $this->messageBus->handle($event);
    }
}