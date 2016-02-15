<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use BookLibrary\Calendar;
use DateTimeImmutable;
use EventSourcing\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

class BookCopy extends AggregateRoot
{
    private $bookCopyId = null;
    private $readerId = null;
    private $dueOn = null;
    private $lent = false;

    protected function applyAdded(BookCopyAddedEvent $bookCopyAddedEvent)
    {
        $this->bookCopyId = $bookCopyAddedEvent->getAggregateId();
    }

    protected function applyLent(BookCopyLentEvent $bookCopyLentEvent)
    {
        $this->readerId = $bookCopyLentEvent->getReaderId();
        $this->dueOn = $bookCopyLentEvent->getDueOn();
        $this->lent = true;
    }

    protected function applyReturned(BookCopyReturnedEvent $bookCopyReturnedEvent)
    {
        $this->readerId = null;
        $this->dueOn = null;
        $this->lent = false;
    }


    protected function applyReturnedLate(BookCopyReturnedLateEvent $bookCopyReturnedLateEvent)
    {
        $this->readerId = null;
        $this->dueOn = null;
        $this->lent = false;
    }

    protected function applyExtension(BookCopyLendingExtendedEvent $bookCopyLendingExtendedEvent)
    {
        $this->dueOn = $bookCopyLendingExtendedEvent->getNewDueDate();
    }

    public static function add(UuidInterface $bookCopyId)
    {
        $copy = new self;

        $copy->apply(new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId));

        return $copy;
    }

    public function lendTo(UuidInterface $readerId, DateTimeImmutable $dueDate)
    {
        if ($this->readerId == $readerId) {
            return;
        }

        if ($this->readerId instanceof UuidInterface) {
            throw new BookCopyAlreadyLentException;
        }

        $this->apply(new BookCopyLentEvent($this->bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueDate));
    }

    public function return ()
    {
        if (!$this->lent) {
            throw new BookCopyNotLentCannotBeExtendedException;
        }

        $returnedOn = Calendar::getCurrentDateTime();

        if ($this->dueOn < $returnedOn) {
            return $this->apply(new BookCopyReturnedLateEvent($returnedOn, $this->bookCopyId, $this->readerId, $returnedOn->diff($this->dueOn)));
        }

        $this->apply(new BookCopyReturnedEvent($returnedOn, $this->bookCopyId, $this->readerId));
    }

    public function extend(DateTimeImmutable $newDue)
    {
        if (!$this->lent) {
            throw new BookCopyNotLentCannotBeExtendedException();
        }

        $this->apply(new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $this->bookCopyId, $newDue));
    }
}