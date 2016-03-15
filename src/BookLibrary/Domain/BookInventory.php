<?php
declare(strict_types = 1);

namespace BookLibrary\Domain;

use Ramsey\Uuid\UuidInterface;

interface BookInventory
{
    public function get(UuidInterface $bookId) : Book;

    public function save(Book $book);
}