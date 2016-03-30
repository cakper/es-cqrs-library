<?php
declare(strict_types = 1);
namespace EventSourcing\EventStore;

use Iterator;
use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function saveEvents(UuidInterface $aggregateId, int $aggregateType, int $originatingVersion, Iterator $events);

    public function findEventsForAggregate(UuidInterface $aggregateId) : Iterator;

    public function findEventsOfClasses(array $classes) : Iterator;

    public function findAllEvents() : Iterator;
}