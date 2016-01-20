<?php
declare(strict_types = 1);
namespace EventSourcing;

interface EventStore
{
    public function saveEvents(AggregateId $aggregateId, Events $events, AggregateVersion $expectedVersion);

    public function getEventsForAggregate(AggregateId $aggregateId);
}