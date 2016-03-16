<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookExtendedEvent implements Event
{
    private $extendedOn;
    private $bookCopyId;
    private $newDueDate;
    private $readerId;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $extendedOn, DateTimeImmutable $newDueDate)
    {
        $this->extendedOn = $extendedOn;
        $this->bookCopyId = $bookCopyId;
        $this->newDueDate = $newDueDate;
        $this->readerId = $readerId;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function getBookCopyId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getExtendedOn() : DateTimeImmutable
    {
        return $this->extendedOn;
    }

    public function getNewDueDate() : DateTimeImmutable
    {
        return $this->newDueDate;
    }
}