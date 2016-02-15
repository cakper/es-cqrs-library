<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use Doctrine\ORM\EntityManager;
use EventSourcing\AggregateRoot;
use EventSourcing\Event as DomainEvent;
use EventSourcing\OptimisticConcurrencyException;
use Infrastructure\EventStore\Doctrine\Aggregate;
use Infrastructure\EventStore\Doctrine\Event;
use Ramsey\Uuid\UuidInterface;

class EventSourcedBookInventory implements BookInventory
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(UuidInterface $bookId)
    {
        $eventRepository = $this->entityManager->getRepository(Event::class);
        $events = $eventRepository->findByAggregateId($bookId);
        if (count($events) === 0) {
            return null;
        }

        return Book::loadFromHistory(array_map(function (Event $event) : DomainEvent {
            return $event->getDomainEvent();
        }, $events));
    }

    public function save(Book $book)
    {
        $this->entityManager->transactional(function () use ($book) {

            $aggregate = $this->entityManager->find(Aggregate::class, $book->getAggregateId());

            if (!$aggregate instanceof Aggregate) {
                $aggregate = new Aggregate($book->getAggregateId(), $book::getType(), AggregateRoot::VERSION_NEW);
                $this->entityManager->persist($aggregate);
            }

            $version = $book->getOriginatingVersion();

            if ($aggregate->version != $version) {
                throw new OptimisticConcurrencyException();
            }

            foreach ($book->getChanges() as $domainEvent) {
                $event = new Event($book->getAggregateId(), ++$version, $domainEvent);
                $this->entityManager->persist($event);
            }

            $aggregate->version = $version;
        });
    }
}