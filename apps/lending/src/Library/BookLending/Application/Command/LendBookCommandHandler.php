<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use Library\BookLending\Domain\BookInventory;

class LendBookCommandHandler
{
    private $bookInventory;

    public function __construct(BookInventory $bookInventory)
    {
        $this->bookInventory = $bookInventory;
    }

    public function handle(LendBookCommand $command)
    {
        $book = $this->bookInventory->get($command->bookId);
        $book->lendTo($command->readerId, $command->dueOn);
        $this->bookInventory->save($book);
    }
}