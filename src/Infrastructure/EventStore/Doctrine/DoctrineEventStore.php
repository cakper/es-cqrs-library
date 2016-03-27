<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use EventSourcing\AggregateNotFoundException;
use EventSourcing\AggregateRoot;
use EventSourcing\EventStore;
use EventSourcing\EventStore\TypeMapping;
use EventSourcing\OptimisticConcurrencyException;
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
    /**
     * @var TypeMapping
     */
    private $typeMapping;

    public function __construct(EntityManager $entityManager, Serializer $serializer, TypeMapping $typeMapping)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->typeMapping = $typeMapping;
    }

    public function saveEvents(UuidInterface $aggregateId, int $aggregateType, int $originatingVersion, Iterator $events)
    {
        $this->entityManager->transactional(function () use ($aggregateId, $aggregateType, $originatingVersion, $events) {
            $aggregate = $this->entityManager->find(Aggregate::class, $aggregateId);
            if (!$aggregate instanceof Aggregate) {
                $aggregate = new Aggregate($aggregateId, $aggregateType, AggregateRoot::VERSION_NEW);
            }

            if ($aggregate->version != $originatingVersion) {
                throw new OptimisticConcurrencyException();
            }

            foreach ($events as $domainEvent) {
                $event = new Event($aggregateId, ++$originatingVersion, $this->serializer->serialize($domainEvent, 'json'), $this->typeMapping->forEventClass(get_class($domainEvent)));
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
        $query->setParameter('types', $this->typeMapping->forEventClasses($classes));

        foreach ($query->iterate(null, Query::HYDRATE_ARRAY) as $row) {
            yield $this->serializer->deserialize($row[0]['data'], $row[0]['type'], 'json');
        }
    }
}