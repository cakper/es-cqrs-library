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
        $this->bookCopyId = $bookCopyAddedEvent->getBookCopyId();
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

    protected function applyExtended(BookExtendedEvent $bookCopyLendingExtendedEvent)
    {
        $this->dueOn = $bookCopyLendingExtendedEvent->getNewDueDate();
    }

    public static function add(UuidInterface $bookCopyId, string $title)
    {
        $copy = new self;

        $copy->apply(new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $title));

        return $copy;
    }

    public function lendTo(UuidInterface $readerId, DateTimeImmutable $dueDate)
    {
        if ($this->readerId instanceof UuidInterface) {
            throw new BookAlreadyLentException;
        }

        $this->apply(new BookLentEvent($this->bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueDate));
    }

    public function return ()
    {
        if (!$this->lent) {
            throw new BookNotLentCannotBeReturnedException;
        }

        $this->apply(new BookReturnedEvent($this->bookCopyId, $this->readerId, Calendar::getCurrentDateTime(), $this->dueOn));
    }

    public function extend(DateTimeImmutable $newDue)
    {
        if (!$this->lent) {
            throw new BookNotLentCannotBeExtendedException;
        }

        $this->apply(new BookExtendedEvent($this->bookCopyId, $this->readerId, Calendar::getCurrentDateTime(), $newDue));
    }

    public function getId() : UuidInterface
    {
        return $this->bookCopyId;
    }
}