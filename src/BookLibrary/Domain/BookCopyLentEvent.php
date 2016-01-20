<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookCopyLentEvent implements Event
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
    private $lentOn;
    /**
     * @var DateTimeImmutable
     */
    private $dueOn;

    /**
     * BookLentEvent constructor.
     *
     * @param DateTimeImmutable $lentOn
     * @param DateTimeImmutable $dueOn
     * @param BookCopyId        $bookCopyId
     * @param ReaderId          $readerId
     */
    public function __construct(DateTimeImmutable $lentOn, DateTimeImmutable $dueOn, BookCopyId $bookCopyId, ReaderId $readerId)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->lentOn = $lentOn;
        $this->dueOn = $dueOn;
    }

    public function getDueOn() : DateTimeImmutable
    {
        return $this->dueOn;
    }

    public function getLentOn() : DateTimeImmutable
    {
        return $this->lentOn;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->bookCopyId;
    }

    public function getReaderId() : ReaderId
    {
        return $this->readerId;
    }
}