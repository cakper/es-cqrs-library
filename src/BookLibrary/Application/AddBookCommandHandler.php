<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use BookLibrary\Domain\Book;
use BookLibrary\Domain\BookInventory;

class AddBookCommandHandler
{
    private $bookInventory;

    public function __construct(BookInventory $bookInventory)
    {
        $this->bookInventory = $bookInventory;
    }

    public function handle(AddBookCommand $command)
    {
        $this->bookInventory->save(Book::add($command->bookId, $command->title));
    }
}