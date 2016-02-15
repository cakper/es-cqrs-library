<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use BookLibrary\Domain\Book;
use BookLibrary\Domain\BookInventory;

class ExtendBookCommandHandler
{
    private $bookInventory;

    public function __construct(BookInventory $bookInventory)
    {
        $this->bookInventory = $bookInventory;
    }

    public function handle(ExtendBookCommand $command)
    {
        $book = $this->bookInventory->get($command->bookId);
        if (!$book instanceof Book) {
            throw new \InvalidArgumentException('Book not found');
        }

        $book->extend($command->newDue);
        $this->bookInventory->save($book);
    }
}