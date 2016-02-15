<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\EntityManager;
use EventSourcing\AggregateRoot;
use EventSourcing\Event as DomainEvent;
use EventSourcing\Events;
use EventSourcing\EventStore;
use EventSourcing\OptimisticConcurrencyException;
use Ramsey\Uuid\UuidInterface;

class DoctrineEventStore implements EventStore
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveEvents(UuidInterface $aggregateId, string $aggregateType, int $originatingVersion, Events $events)
    {
        $this->entityManager->transactional(function () use ($aggregateId, $aggregateType, $originatingVersion, $events) {

            $aggregate = $this->entityManager->find(Aggregate::class, $aggregateId);

            if (!$aggregate instanceof Aggregate) {
                $aggregate = new Aggregate($aggregateId, $aggregateType, AggregateRoot::VERSION_NEW);
                $this->entityManager->persist($aggregate);
            }

            if ($aggregate->version != $originatingVersion) {
                throw new OptimisticConcurrencyException();
            }

            $events->each(function (DomainEvent $domainEvent) use ($aggregateId, $originatingVersion) {
                $event = new Event($aggregateId, ++$originatingVersion, $domainEvent);
                $this->entityManager->persist($event);
            });

            $aggregate->version = $originatingVersion;
        });
    }

    public function findEventsForAggregate(UuidInterface $aggregateId) : Events
    {
        $eventRepository = $this->entityManager->getRepository(Event::class);
        $events = $eventRepository->findByAggregateId($aggregateId);

        return new Events(array_map(function (Event $event) : DomainEvent {
            return $event->getDomainEvent();
        }, $events));
    }
}