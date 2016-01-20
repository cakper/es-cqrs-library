<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookCopyAddedEvent implements Event
{
    /**
     * @var BookCopyId
     */
    private $bookCopyId;

    /**
     * @var Isbn10
     */
    private $bookEditionId;
    /**
     * @var DateTimeImmutable
     */
    private $addedOn;

    /**
     * BookCopyAddedEvent constructor.
     *
     * @param DateTimeImmutable $addedOn
     * @param BookCopyId        $bookCopyId
     * @param Isbn10            $bookEditionId
     */
    public function __construct(DateTimeImmutable $addedOn, BookCopyId $bookCopyId, Isbn10 $bookEditionId)
    {
        $this->bookCopyId = $bookCopyId;
        $this->bookEditionId = $bookEditionId;
        $this->addedOn = $addedOn;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getAddedOn()
    {
        return $this->addedOn;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->bookCopyId;
    }

    /**
     * @return Isbn10
     */
    public function getBookEditionId()
    {
        return $this->bookEditionId;
    }
}