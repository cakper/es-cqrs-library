<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class LendBookCommand
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