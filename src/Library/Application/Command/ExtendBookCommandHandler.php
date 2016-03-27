<?php
declare(strict_types = 1);
namespace Library\Application\Command;

use Library\Domain\BookInventory;

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