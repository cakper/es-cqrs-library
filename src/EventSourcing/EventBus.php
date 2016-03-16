<?php

namespace EventSourcing;

interface EventBus
{
    public function handle(Event $event);
}