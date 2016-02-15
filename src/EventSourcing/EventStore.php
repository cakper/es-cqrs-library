<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function saveEvents(UuidInterface $aggregateId, Events $events, int $expectedAggregateVersion);

    public function findEventsForAggregate(UuidInterface $aggregateId);
}