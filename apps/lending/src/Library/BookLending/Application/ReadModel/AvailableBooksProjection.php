<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\ReadModel;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Infrastructure\EventStore\Doctrine\BookAvailableView;
use Infrastructure\EventStore\Doctrine\DoctrineProjection;
use Library\BookLending\Domain\BookAddedEvent;
use Library\BookLending\Domain\BookLentEvent;
use Library\BookLending\Domain\BookReturnedEvent;

class AvailableBooksProjection extends DoctrineProjection
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
    private $books = [];

    /**
     * AvailableBooksProjection constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(BookAvailableView::class);
        parent::__construct($entityManager);
    }

    public function handleNewBook(BookAddedEvent $event)
    {
        $this->books[$event->getBookCopyId()->toString()] = $event->getTitle();
        $this->titles[$event->getBookCopyId()->toString()] = $event->getTitle();
    }

    public function handleLent(BookLentEvent $event)
    {
        unset($this->books[$event->getBookCopyId()->toString()]);
    }

    public function handleReturned(BookReturnedEvent $event)
    {
        $this->books[$event->getBookCopyId()->toString()] = $this->titles[$event->getBookCopyId()->toString()];
    }

    public function persist()
    {
        foreach ($this->books as $id => $title) {
            $this->entityManager->persist(new BookAvailableView($id, $title));
        }
        $this->entityManager->flush();
    }

//    public function handleNewBook(BookAddedEvent $event)
//    {
//        $book = new BookAvailableView($event->getBookCopyId(), $event->getTitle());
//        $this->entityManager->persist($book);
//        $this->entityManager->flush($book);
//        $this->titles[$event->getBookCopyId()->toString()] = $event->getTitle();
//    }
//
//    public function handleLent(BookLentEvent $event)
//    {
//        $view = $this->repository->find($event->getBookCopyId());
//        $this->entityManager->remove($view);
//        $this->entityManager->flush($view);
//    }
//
//    public function handleReturned(BookReturnedEvent $event)
//    {
//        $book = new BookAvailableView($event->getBookCopyId(), $this->titles[$event->getBookCopyId()->toString()]);
//        $this->entityManager->persist($book);
//        $this->entityManager->flush($book);
//    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function getViewClasses() : array
    {
        return [BookAvailableView::class];
    }
}