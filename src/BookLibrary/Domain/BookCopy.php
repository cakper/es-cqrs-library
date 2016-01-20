<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateRoot;

class BookCopyState extends AggregateRoot
{
    /**
     * @var BookCopyId
     */
    protected $bookCopyId;

    /**
     * @var boolean
     */
    protected $lent;

    /**
     * @var ReaderId
     */
    protected $readerId;

    /**
     * @var DateTimeImmutable
     */
    protected $dueOn;

    protected function applyCopyAdded(BookCopyAddedEvent $copyAddedEvent)
    {
        $this->bookCopyId = $copyAddedEvent->getAggregateId();
    }

    protected function applyLent(BookCopyLentEvent $lentEvent)
    {
        $this->lent = true;
        $this->readerId = $lentEvent->getReaderId();
        $this->dueOn = $lentEvent->getDueOn();
    }

    protected function applyReturned(BookCopyReturnedEvent $returnedEvent)
    {
        $this->lent = false;
    }

    protected function applyReturnedLate(BookCopyReturnedLateEvent $returnedLateEvent)
    {
        $this->lent = false;
    }

    protected function applyExtension(BookCopyLendingExtendedEvent $bookExtendedEvent)
    {
        $this->dueOn = $bookExtendedEvent->getNewDueDate();
    }
}

class BookCopy extends BookCopyState
{
    public static function add(BookCopyId $bookCopyId, Isbn10 $bookEditionIsbn)
    {
        $copy = new self;
        $copy->apply(new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $bookEditionIsbn));

        return $copy;
    }

    public function lendTo(ReaderId $readerId, DateTimeImmutable $dueOn)
    {
        if ($this->lent) {
            throw new BookCopyAlreadyLentException();
        }

        $this->apply(new BookCopyLentEvent(Calendar::getCurrentDateTime(), $dueOn, $this->bookCopyId, $readerId));
    }

    public function return ()
    {
        if (!$this->lent) {
            throw new BookCopyNotLentCannotBeReturnedException();
        }

        $now = Calendar::getCurrentDateTime();

        if ($now >= $this->dueOn) {
            $this->apply(new BookCopyReturnedLateEvent($now, $this->bookCopyId, $this->readerId, $now->diff($this->dueOn)));

            return;
        }

        $this->apply(new BookCopyReturnedEvent($now, $this->bookCopyId, $this->readerId));
    }

    public function extend(DateTimeImmutable $newDue)
    {
        if (!$this->lent) {
            throw new BookCopyNotLentCannotBeExtendedException();
        }

        $this->apply(new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $this->bookCopyId, $newDue));
    }
}