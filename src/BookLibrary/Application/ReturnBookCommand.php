<?php
declare(strict_types = 1);

namespace BookLibrary\Application;

use Ramsey\Uuid\UuidInterface;

class ReturnBookCommand
{
    public $bookId;

    public function __construct(UuidInterface $bookId)
    {
        $this->bookId = $bookId;
    }
}