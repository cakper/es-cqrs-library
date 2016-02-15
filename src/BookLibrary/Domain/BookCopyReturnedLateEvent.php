<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateInterval;
use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookCopyReturnedLateEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $returnedOn;
    private $lateBy;

    public function __construct(DateTimeImmutable $returnedOn, UuidInterface $bookCopyId, UuidInterface $readerId, DateInterval $lateBy)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
        $this->lateBy = $lateBy;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function getReturnedOn() : DateTimeImmutable
    {
        return $this->returnedOn;
    }

    public function getLateBy() : DateInterval
    {
        return $this->lateBy;
    }
}