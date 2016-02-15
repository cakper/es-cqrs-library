<?php
declare(strict_types = 1);

namespace BookLibrary\Application;

use BookLibrary\Domain\Book;
use BookLibrary\Domain\BookInventory;

class ReturnBookCommandHandler
{
    private $bookInventory;

    public function __construct(BookInventory $bookInventory)
    {
        $this->bookInventory = $bookInventory;
    }

    public function handle(ReturnBookCommand $command)
    {
        $book = $this->bookInventory->get($command->bookId);
        if (!$book instanceof Book) {
            throw new \InvalidArgumentException('Book not found');
        }

        $book->return();
        $this->bookInventory->save($book);
    }
}