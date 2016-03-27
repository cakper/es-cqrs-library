<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use DateTimeImmutable;
use EventSourcing\Messaging\Command;
use Ramsey\Uuid\UuidInterface;

class ExtendBookCommand implements Command
{
    public $bookId;
    public $newDue;

    public function __construct(UuidInterface $bookId, DateTimeImmutable $newDue)
    {
        $this->bookId = $bookId;
        $this->newDue = $newDue;
    }
}