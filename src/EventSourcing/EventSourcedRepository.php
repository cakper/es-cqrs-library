<?php

namespace EventSourcing;

use Infrastructure\Domain\Type;
use Ramsey\Uuid\UuidInterface;
use SimpleBus\Message\Bus\MessageBus;

class EventSourcedRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var MessageBus
     */
    private $eventBus;

    public function __construct(EventStore $eventStore, MessageBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function get(UuidInterface $aggregateId, $aggregateClass) : AggregateRoot
    {
        $events = $this->eventStore->findEventsForAggregate($aggregateId);

        if (count($events) === 0) {
            throw new AggregateNotFoundException($aggregateId);
        }

        return call_user_func_array(
            [$aggregateClass, 'loadFromHistory'],
            [$events]
        );
    }

    public function save(AggregateRoot $aggregateRoot)
    {
        $this->eventStore->saveEvents(
            $aggregateRoot->getId(),
            Type::forAggregate($aggregateRoot),
            $aggregateRoot->getOriginatingVersion(),
            $aggregateRoot->getChanges()
        );
        $aggregateRoot->getChanges()->each(function (Event $event) {
            $this->eventBus->handle($event);
        });
        $aggregateRoot->markChangesAsCommitted();
    }

}