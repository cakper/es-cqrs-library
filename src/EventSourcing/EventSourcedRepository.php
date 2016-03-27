<?php

namespace EventSourcing;

use EventSourcing\EventStore\TypeMapping;
use EventSourcing\Messaging\EventBus;
use Ramsey\Uuid\UuidInterface;

class EventSourcedRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventBus
     */
    private $eventBus;
    /**
     * @var TypeMapping
     */
    private $typeMapping;

    public function __construct(EventStore $eventStore, EventBus $eventBus, TypeMapping $typeMapping)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->typeMapping = $typeMapping;
    }

    public function get(UuidInterface $aggregateId, $aggregateClass) : AggregateRoot
    {
        return call_user_func_array(
            [$aggregateClass, 'loadFromHistory'],
            [$this->eventStore->findEventsForAggregate($aggregateId)]
        );
    }

    public function save(AggregateRoot $aggregateRoot)
    {
        $this->eventStore->saveEvents(
            $aggregateRoot->getId(),
            $this->typeMapping->forAggregateClass(get_class($aggregateRoot)),
            $aggregateRoot->getOriginatingVersion(),
            $aggregateRoot->getChanges()
        );

        foreach ($aggregateRoot->getChanges() as $event) {
            $this->eventBus->handle($event);
        }
        $aggregateRoot->markChangesAsCommitted();
    }
}