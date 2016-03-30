<?php
declare(strict_types = 1);

namespace Library\BookLending\Application\Command;

use Library\BookLending\Domain\BookInventory;

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
        $book->return();
        $this->bookInventory->save($book);
    }
}