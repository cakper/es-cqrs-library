<?php
declare(strict_types = 1);
namespace Library\BookLending\Domain;

use DateTimeImmutable;
use EventSourcing\Messaging\Event;
use Ramsey\Uuid\UuidInterface;

class BookAddedEvent implements Event
{
    private $bookCopyId;
    private $addedOn;
    private $title;

    public function __construct(DateTimeImmutable $addedOn, UuidInterface $bookCopyId, string $title)
    {
        $this->bookCopyId = $bookCopyId;
        $this->addedOn = $addedOn;
        $this->title = $title;
    }

    public function getAddedOn() : DateTimeImmutable
    {
        return $this->addedOn;
    }

    public function getBookCopyId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getTitle()
    {
        return $this->title;
    }
}