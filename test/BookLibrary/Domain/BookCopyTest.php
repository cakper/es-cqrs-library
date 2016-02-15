<?php

namespace test\BookLibrary\Domain;

use BookLibrary\Calendar;
use BookLibrary\Domain\BookCopy;
use BookLibrary\Domain\BookCopyAddedEvent;
use BookLibrary\Domain\BookCopyAlreadyLentException;
use BookLibrary\Domain\BookCopyLendingExtendedEvent;
use BookLibrary\Domain\BookCopyLentEvent;
use BookLibrary\Domain\BookCopyNotLentCannotBeExtendedException;
use BookLibrary\Domain\BookCopyReturnedEvent;
use BookLibrary\Domain\BookCopyReturnedLateEvent;
use Ramsey\Uuid\Uuid;

class BookCopyTest extends ScenarioTest
{
    protected function getAggregateClass()
    {
        return BookCopy::class;
    }

    public function testAddNewBookCopy()
    {
        $bookCopyId = Uuid::uuid4();;

        $this
            ->when('add', [$bookCopyId])
            ->then([new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId)]);
    }

    public function testLendingBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();
        $returnDueDate = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId)])
            ->when('lendTo', [$readerId, $returnDueDate])
            ->then([new BookCopyLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $returnDueDate)]);
    }

    public function testLendingBookThatWasAlreadyLent()
    {
        $readerId = Uuid::uuid4();
        $secondReaderId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime()->modify('+30 days'))
            ])
            ->when('lendTo', [$secondReaderId, Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookCopyAlreadyLentException::class);
    }

    public function testLendingBookThatWasAlreadyLentToTheSameReader()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('lendTo', [$readerId, $dueOn])
            ->then([]);
    }

    public function testReturnBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('return')
            ->then([new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testReturnBookThatWasAlreadyReturned()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn),
                new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)
            ])
            ->when('return')
            ->then(BookCopyNotLentCannotBeExtendedException::class);
    }

    public function testReturnBookLate()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $lentOn = Calendar::getCurrentDateTime()->modify('-32 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('-2 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
            ])
            ->when('return')
            ->then([new BookCopyReturnedLateEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId, Calendar::getCurrentDateTime()->diff($dueOn))]);
    }

    public function testExtendBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $lentOn = Calendar::getCurrentDateTime()->modify('-28 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('+2 days');
        $newDue = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, $lentOn, $dueOn)
            ])
            ->when('extend', [$newDue])
            ->then([new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)]);
    }

    public function testReturnExtendedBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $lentOn = Calendar::getCurrentDateTime()->modify('-28 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('+2 days');
        $newDue = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookCopyLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
                new BookCopyLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)
            ])
            ->when('return')
            ->then([new BookCopyReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testExtendBookThatWasNotLent()
    {
        $this
            ->given([
                new BookCopyAddedEvent(Calendar::getCurrentDateTime(), Uuid::uuid4())
            ])
            ->when('extend', [Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookCopyNotLentCannotBeExtendedException::class);
    }
}
