<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\EntityManager;
use EventSourcing\AggregateRoot;
use EventSourcing\Event as DomainEvent;
use EventSourcing\EventStore;
use EventSourcing\EventStream;
use EventSourcing\OptimisticConcurrencyException;
use Infrastructure\Domain\Type;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Serializer;

class DoctrineEventStore implements EventStore
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(EntityManager $entityManager, Serializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function saveEvents(UuidInterface $aggregateId, string $aggregateType, int $originatingVersion, EventStream $eventStream)
    {
        $this->entityManager->transactional(function () use ($aggregateId, $aggregateType, $originatingVersion, $eventStream) {
            $aggregate = $this->entityManager->find(Aggregate::class, $aggregateId);
            if (!$aggregate instanceof Aggregate) {
                $aggregate = new Aggregate($aggregateId, $aggregateType, AggregateRoot::VERSION_NEW);
            }

            if ($aggregate->version != $originatingVersion) {
                throw new OptimisticConcurrencyException();
            }

            $eventStream->each(function (DomainEvent $domainEvent) use ($aggregateId, &$originatingVersion) {
                $event = new Event($aggregateId, ++$originatingVersion, $this->serializer->serialize($domainEvent, 'json'), Type::forEvent($domainEvent));
                $this->entityManager->persist($event);
            });

            $aggregate->version = $originatingVersion;
            $this->entityManager->persist($aggregate);
        });
    }

    public function findEventsForAggregate(UuidInterface $aggregateId) : EventStream
    {
        $query = $this->entityManager->createQuery("SELECT e FROM EventStore:Event e WHERE e.aggregateId = :aggregateId");
        $query->setParameter('aggregateId', $aggregateId->toString());

        return new EventStream(array_map(function ($event) : DomainEvent {
            return $this->serializer->deserialize($event['data'], $event['type'], 'json');
        }, $query->getArrayResult()));
    }
}