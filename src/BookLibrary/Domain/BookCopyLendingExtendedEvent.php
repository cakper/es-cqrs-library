<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookCopyLendingExtendedEvent implements Event
{
    /**
     * @var DateTimeImmutable
     */
    private $extendedOn;
    /**
     * @var ReaderId
     */
    private $bookCopyId;
    /**
     * @var DateTimeImmutable
     */
    private $newDueDate;

    /**
     * BookExtendedEvent constructor.
     *
     * @param DateTimeImmutable $extendedOn
     * @param BookCopyId        $bookCopyId
     * @param DateTimeImmutable $newDueDate
     */
    public function __construct(DateTimeImmutable $extendedOn, BookCopyId $bookCopyId, DateTimeImmutable $newDueDate)
    {
        $this->extendedOn = $extendedOn;
        $this->bookCopyId = $bookCopyId;
        $this->newDueDate = $newDueDate;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->bookCopyId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExtendedOn() : DateTimeImmutable
    {
        return $this->extendedOn;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getNewDueDate() : DateTimeImmutable
    {
        return $this->newDueDate;
    }
}