<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use EventSourcing\AggregateNotFoundException;
use EventSourcing\AggregateRoot;
use EventSourcing\Event as DomainEvent;
use EventSourcing\EventStore;
use EventSourcing\OptimisticConcurrencyException;
use Infrastructure\Domain\Type;
use Iterator;
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

    public function saveEvents(UuidInterface $aggregateId, string $aggregateType, int $originatingVersion, Iterator $eventStream)
    {
        $this->entityManager->transactional(function () use ($aggregateId, $aggregateType, $originatingVersion, $eventStream) {
            $aggregate = $this->entityManager->find(Aggregate::class, $aggregateId);
            if (!$aggregate instanceof Aggregate) {
                $aggregate = new Aggregate($aggregateId, $aggregateType, AggregateRoot::VERSION_NEW);
            }

            if ($aggregate->version != $originatingVersion) {
                throw new OptimisticConcurrencyException();
            }

            foreach ($eventStream as $domainEvent) {
                $event = new Event($aggregateId, ++$originatingVersion, $this->serializer->serialize($domainEvent, 'json'), Type::forEvent($domainEvent));
                $this->entityManager->persist($event);
            }

            $aggregate->version = $originatingVersion;
            $this->entityManager->persist($aggregate);
        });
    }

    public function findEventsForAggregate(UuidInterface $aggregateId) : Iterator
    {
        $aggregate = $this->entityManager->find(Aggregate::class, $aggregateId);

        if (!$aggregate instanceof Aggregate) {
            throw new AggregateNotFoundException($aggregateId);
        }

        $query = $this->entityManager->createQuery("SELECT e FROM EventStore:Event e WHERE e.aggregateId = :aggregateId");
        $query->setParameter('aggregateId', $aggregateId->toString());

        foreach ($query->iterate(null, Query::HYDRATE_ARRAY) as $row) {
            yield $this->serializer->deserialize($row[0]['data'], $row[0]['type'], 'json');
        }
    }

    public function findEventsOfClasses(array $classes) : Iterator
    {
        $query = $this->entityManager->createQuery("SELECT e FROM EventStore:Event e where e.type in (:types)");
        $query->setParameter('types', Type::forEventClasses($classes));

        foreach ($query->iterate(null, Query::HYDRATE_ARRAY) as $row) {
            yield $this->serializer->deserialize($row[0]['data'], $row[0]['type'], 'json');
        }
    }
}