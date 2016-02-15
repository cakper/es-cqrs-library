<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateRoot;
use EventSourcing\Calendar;
use Ramsey\Uuid\UuidInterface;

class Book extends AggregateRoot
{
    private $bookCopyId = null;
    private $readerId = null;
    private $dueOn = null;
    private $lent = false;

    protected function applyAdded(BookAddedEvent $bookCopyAddedEvent)
    {
        $this->bookCopyId = $bookCopyAddedEvent->getAggregateId();
    }

    protected function applyLent(BookLentEvent $bookCopyLentEvent)
    {
        $this->readerId = $bookCopyLentEvent->getReaderId();
        $this->dueOn = $bookCopyLentEvent->getDueOn();
        $this->lent = true;
    }

    protected function applyReturned(BookReturnedEvent $bookCopyReturnedEvent)
    {
        $this->readerId = null;
        $this->dueOn = null;
        $this->lent = false;
    }


    protected function applyReturnedLate(BookReturnedLateEvent $bookCopyReturnedLateEvent)
    {
        $this->readerId = null;
        $this->dueOn = null;
        $this->lent = false;
    }

    protected function applyExtension(BookLendingExtendedEvent $bookCopyLendingExtendedEvent)
    {
        $this->dueOn = $bookCopyLendingExtendedEvent->getNewDueDate();
    }

    public static function add(UuidInterface $bookCopyId)
    {
        $copy = new self;

        $copy->apply(new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId));

        return $copy;
    }

    public function lendTo(UuidInterface $readerId, DateTimeImmutable $dueDate)
    {
        if ($this->readerId == $readerId) {
            return;
        }

        if ($this->readerId instanceof UuidInterface) {
            throw new BookAlreadyLentException;
        }

        $this->apply(new BookLentEvent($this->bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueDate));
    }

    public function return ()
    {
        if (!$this->lent) {
            throw new BookNotLentCannotBeExtendedException;
        }

        $returnedOn = Calendar::getCurrentDateTime();

        if ($this->dueOn < $returnedOn) {
            return $this->apply(new BookReturnedLateEvent($returnedOn, $this->bookCopyId, $this->readerId, $returnedOn->diff($this->dueOn)));
        }

        $this->apply(new BookReturnedEvent($returnedOn, $this->bookCopyId, $this->readerId));
    }

    public function extend(DateTimeImmutable $newDue)
    {
        if (!$this->lent) {
            throw new BookNotLentCannotBeExtendedException();
        }

        $this->apply(new BookLendingExtendedEvent(Calendar::getCurrentDateTime(), $this->bookCopyId, $newDue));
    }
}