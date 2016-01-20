<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookCopyReturnedEvent implements Event
{
    /**
     * @var BookCopyId
     */
    private $bookCopyId;

    /**
     * @var ReaderId
     */
    private $readerId;
    /**
     * @var DateTimeImmutable
     */
    private $returnedOn;

    /**
     * BookReturnedEvent constructor.
     *
     * @param DateTimeImmutable $returnedOn
     * @param BookCopyId        $bookCopyId
     * @param ReaderId          $readerId
     */
    public function __construct(DateTimeImmutable $returnedOn, BookCopyId $bookCopyId, ReaderId $readerId)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
    }

    /**
     * @return ReaderId
     */
    public function getReaderId()
    {
        return $this->readerId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getReturnedOn()
    {
        return $this->returnedOn;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->bookCopyId;
    }
}