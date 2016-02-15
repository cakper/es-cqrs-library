<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Event;
use Ramsey\Uuid\UuidInterface;

class BookAddedEvent implements Event
{
    private $bookCopyId;

    private $addedOn;

    public function __construct(DateTimeImmutable $addedOn, UuidInterface $bookCopyId)
    {
        $this->bookCopyId = $bookCopyId;
        $this->addedOn = $addedOn;
    }

    public function getAddedOn() : DateTimeImmutable
    {
        return $this->addedOn;
    }

    public function getBookCopyId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }
}