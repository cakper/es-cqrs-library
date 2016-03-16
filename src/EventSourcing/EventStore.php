<?php
declare(strict_types = 1);
namespace EventSourcing;

use Iterator;
use Ramsey\Uuid\UuidInterface;

interface EventStore
{
    public function saveEvents(UuidInterface $aggregateId, string $aggregateType, int $originatingVersion, Iterator $eventStream);

    public function findEventsForAggregate(UuidInterface $aggregateId) : Iterator;

    public function findEventsOfClasses(array $classes) : Iterator;
}