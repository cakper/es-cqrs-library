<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookCopyReturnedEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $returnedOn;

    public function __construct(DateTimeImmutable $returnedOn, UuidInterface $bookCopyId, UuidInterface $readerId)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function getReturnedOn() : DateTimeImmutable
    {
        return $this->returnedOn;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }
}