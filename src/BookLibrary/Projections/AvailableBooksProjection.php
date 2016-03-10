<?php
declare(strict_types = 1);
namespace BookLibrary\Projections;

use BookLibrary\Domain\BookAddedEvent;
use BookLibrary\Domain\BookLentEvent;
use BookLibrary\Domain\BookReturnedEvent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Infrastructure\EventStore\Doctrine\BookAvailableView;

class AvailableBooksProjection
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    private $titles = [];

    /**
     * AvailableBooksProjection constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(BookAvailableView::class);
    }

    public function handleNewBook(BookAddedEvent $event)
    {
        $book = new BookAvailableView($event->getAggregateId(), $event->getTitle());
        $this->entityManager->persist($book);
        $this->entityManager->flush($book);

        $this->titles[$event->getAggregateId()->toString()] = $event->getTitle();
    }

    public function handleLent(BookLentEvent $event)
    {
        $view = $this->repository->find($event->getAggregateId());
        $this->entityManager->remove($view);
        $this->entityManager->flush($view);
    }

    public function handleReturned(BookReturnedEvent $event)
    {
        $book = new BookAvailableView($event->getAggregateId(), $this->titles[$event->getAggregateId()->toString()]);
        $this->entityManager->persist($book);
        $this->entityManager->flush($book);
    }
}