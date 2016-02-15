<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookLendingExtendedEvent implements Event
{
    private $extendedOn;
    private $bookCopyId;
    private $newDueDate;

    public function __construct(DateTimeImmutable $extendedOn, UuidInterface $bookCopyId, DateTimeImmutable $newDueDate)
    {
        $this->extendedOn = $extendedOn;
        $this->bookCopyId = $bookCopyId;
        $this->newDueDate = $newDueDate;
    }

    public function getAggregateId() : UuidInterface
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