<?php

namespace EventSourcing\Messaging;

interface CommandBus
{
    public function handle(Command $command);
}