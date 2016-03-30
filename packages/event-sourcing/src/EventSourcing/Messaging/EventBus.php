<?php

namespace EventSourcing\Messaging;

interface EventBus
{
    public function handle(Event $event);
}