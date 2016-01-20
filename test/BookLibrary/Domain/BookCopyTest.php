<?php

namespace test\BookLibrary\Domain;

use BookLibrary\Domain\BookCopyAlreadyLentException;
use BookLibrary\Domain\BookCopyNotLentCannotBeReturnedException;
use BookLibrary\Domain\BookCopy;
use BookLibrary\Domain\BookCopyAddedEvent;
use BookLibrary\Domain\BookCopyId;
use BookLibrary\Domain\BookCopyLendingExtendedEvent;
use BookLibrary\Domain\BookCopyLentEvent;
use BookLibrary\Domain\BookCopyNotLentCannotBeExtendedException;
use BookLibrary\Domain\BookCopyReturnedEvent;
use BookLibrary\Domain\BookCopyReturnedLateEvent;
use BookLibrary\Domain\Calendar;
use BookLibrary\Domain\Isbn10;
use BookLibrary\Domain\ReaderId;

class BookCopyTest extends ScenarioTest
{
    protected function getAggregateClass()
    {
        return BookCopy::class;
    }

    public function testAddNewBookCopy()
    {
        $editionIsbn = new Isbn10('121312');
        $bookCopyId = BookCopyId::generate();

        $this
            ->when('add', [$bookCopyId, $editionIsbn])
            ->then([new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $editionIsbn)]);
    }

    public function testLendingBook()
    {
        $readerId = ReaderId::generate();
        $bookCopyId = BookCopyId::generate();
        $returnDueDate = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312'))])
            ->when('lendTo', [$readerId, $returnDueDate])
            ->then([new BookCopyLentEvent(Calendar::getCurrentDateTime(), $returnDueDate, $bookCopyId, $readerId)]);
    }

    public function testLendingBookThatWasAlreadyLent()
    {
        $readerId = ReaderId::generate();
        $bookCopyId = BookCopyId::generate();

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent(Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime()->modify('+30 days'), $bookCopyId, $readerId)
            ])
            ->when('lendTo', [$readerId, Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookCopyAlreadyLentException::class);
    }

    public function testReturnBook()
    {
        $readerId = ReaderId::generate();
        $bookCopyId = BookCopyId::generate();

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent(Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime()->modify('+30 days'), $bookCopyId, $readerId)
            ])
            ->when('return')
            ->then([new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testReturnBookThatWasAlreadyReturned()
    {
        $readerId = ReaderId::generate();
        $bookCopyId = BookCopyId::generate();

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent(Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime()->modify('+30 days'), $bookCopyId, $readerId),
                new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)
            ])
            ->when('return')
            ->then(BookCopyNotLentCannotBeReturnedException::class);
    }

    public function testReturnBookLate()
    {
        $readerId = ReaderId::generate();
        $bookCopyId = BookCopyId::generate();
        $lentOn = Calendar::getCurrentDateTime()->modify('-32 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('-2 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent($lentOn, $dueOn, $bookCopyId, $readerId)
            ])
            ->when('return')
            ->then([new BookCopyReturnedLateEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId, Calendar::getCurrentDateTime()->diff($dueOn))]);
    }

    public function testExtendBook()
    {
        $bookCopyId = BookCopyId::generate();
        $newDue = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent(Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime(), $bookCopyId, ReaderId::generate())
            ])
            ->when('extend', [$newDue])
            ->then([new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)]);
    }

    public function testReturnExtendedBook()
    {
        $bookCopyId = BookCopyId::generate();
        $readerId = ReaderId::generate();
        $newDue = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, new Isbn10('121312')),
                new BookCopyLentEvent(Calendar::getCurrentDateTime()->modify('-32 days'), Calendar::getCurrentDateTime()->modify('-2 days'), $bookCopyId, $readerId),
                new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)
            ])
            ->when('return')
            ->then([new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testExtendBookThatWasNotLent()
    {
        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), BookCopyId::generate(), new Isbn10('121312')),
            ])
            ->when('extend', [Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookCopyNotLentCannotBeExtendedException::class);
    }
}