<?php

namespace EventSourcing;

interface CommandBus
{
    public function handle(Command $command);
}