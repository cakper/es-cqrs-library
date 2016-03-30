<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use Library\BookLending\Domain\BookInventory;

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
        $book->extend($command->newDue);
        $this->bookInventory->save($book);
    }
}