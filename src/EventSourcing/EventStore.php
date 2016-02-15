<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function saveEvents(UuidInterface $aggregateId, string $aggregateType, int $originatingVersion, Events $events);

    public function findEventsForAggregate(UuidInterface $aggregateId);
}