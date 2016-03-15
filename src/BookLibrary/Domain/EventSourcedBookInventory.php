<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use EventSourcing\EventSourcedRepository;
use Ramsey\Uuid\UuidInterface;

class EventSourcedBookInventory implements BookInventory
{
    private $eventSourcedRepository;

    public function __construct(EventSourcedRepository $eventSourcedRepository)
    {
        $this->eventSourcedRepository = $eventSourcedRepository;
    }

    public function get(UuidInterface $bookId) : Book
    {
        return $this->eventSourcedRepository->get($bookId, Book::class);
    }

    public function save(Book $book)
    {
        $this->eventSourcedRepository->save($book);
    }
}