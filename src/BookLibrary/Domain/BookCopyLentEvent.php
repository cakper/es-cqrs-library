<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookCopyLentEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $lentOn;
    private $dueOn;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $lentOn, DateTimeImmutable $dueOn)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->lentOn = $lentOn;
        $this->dueOn = $dueOn;
    }

    public function getAggregateId() : UuidInterface
    {
        $this->bookCopyId;
    }

    public function getDueOn() : DateTimeImmutable
    {
        return $this->dueOn;
    }

    public function getLentOn() : DateTimeImmutable
    {
        return $this->lentOn;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }
}