<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use DateTimeImmutable;
use EventSourcing\Messaging\Command;
use Ramsey\Uuid\UuidInterface;

class LendBookCommand implements Command
{
    public $bookId;
    public $readerId;
    public $dueOn;

    public function __construct(UuidInterface $bookId, UuidInterface $readerId, DateTimeImmutable $dueOn)
    {
        $this->bookId = $bookId;
        $this->readerId = $readerId;
        $this->dueOn = $dueOn;
    }
}