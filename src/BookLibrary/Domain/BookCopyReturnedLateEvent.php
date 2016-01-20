<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateInterval;
use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookCopyReturnedLateEvent implements Event
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
     * @var DateInterval
     */
    private $lateBy;

    /**
     * BookReturnedLateEvent constructor.
     *
     * @param DateTimeImmutable $returnedOn
     * @param BookCopyId        $bookCopyId
     * @param ReaderId          $readerId
     * @param DateInterval      $lateBy
     */
    public function __construct(DateTimeImmutable $returnedOn, BookCopyId $bookCopyId, ReaderId $readerId, DateInterval $lateBy)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
        $this->lateBy = $lateBy;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->bookCopyId;
    }

    public function getReaderId() : ReaderId
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