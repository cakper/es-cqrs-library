<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookReturnedEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $returnedOn;
    private $dueOn;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $returnedOn, DateTimeImmutable $dueOn)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
        $this->dueOn = $dueOn;
    }

    public function getDueOn() : DateTimeImmutable
    {
        return $this->dueOn;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function getReturnedOn() : DateTimeImmutable
    {
        return $this->returnedOn;
    }

    public function getBookCopyId() : UuidInterface
    {
        return $this->bookCopyId;
    }
}