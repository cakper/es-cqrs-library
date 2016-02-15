<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class ExtendBookCommand
{
    public $bookId;
    public $newDue;

    public function __construct(UuidInterface $bookId, DateTimeImmutable $newDue)
    {
        $this->bookId = $bookId;
        $this->newDue = $newDue;
    }
}