<?php

namespace test\BookLibrary\Domain;

use BookLibrary\Domain\Book;
use BookLibrary\Domain\BookAddedEvent;
use BookLibrary\Domain\BookAlreadyLentException;
use BookLibrary\Domain\BookLendingExtendedEvent;
use BookLibrary\Domain\BookLentEvent;
use BookLibrary\Domain\BookNotLentCannotBeExtendedException;
use BookLibrary\Domain\BookReturnedEvent;
use BookLibrary\Domain\BookReturnedLateEvent;
use EventSourcing\Calendar;
use Ramsey\Uuid\Uuid;
use test\BookLibrary\ScenarioTest;

class BookTest extends ScenarioTest
{
    protected function getAggregateClass()
    {
        return Book::class;
    }

    public function testAddNewBookCopy()
    {
        $bookCopyId = Uuid::uuid4();;

        $this
            ->when('add', [$bookCopyId])
            ->then([new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId)]);
    }

    public function testLendingBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();
        $returnDueDate = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId)])
            ->when('lendTo', [$readerId, $returnDueDate])
            ->then([new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $returnDueDate)]);
    }

    public function testLendingBookThatWasAlreadyLent()
    {
        $readerId = Uuid::uuid4();
        $secondReaderId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), Calendar::getCurrentDateTime()->modify('+30 days'))
            ])
            ->when('lendTo', [$secondReaderId, Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookAlreadyLentException::class);
    }

    public function testLendingBookThatWasAlreadyLentToTheSameReader()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('return')
            ->then([new BookReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testReturnBookThatWasAlreadyReturned()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn),
                new BookReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)
            ])
            ->when('return')
            ->then(BookNotLentCannotBeExtendedException::class);
    }

    public function testReturnBookLate()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $lentOn = Calendar::getCurrentDateTime()->modify('-32 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('-2 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
            ])
            ->when('return')
            ->then([new BookReturnedLateEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId, Calendar::getCurrentDateTime()->diff($dueOn))]);
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn)
            ])
            ->when('extend', [$newDue])
            ->then([new BookLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)]);
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
                new BookLendingExtendedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $newDue)
            ])
            ->when('return')
            ->then([new BookReturnedEvent(Calendar::getCurrentDateTime(), $bookCopyId, $readerId)]);
    }

    public function testExtendBookThatWasNotLent()
    {
        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), Uuid::uuid4())
            ])
            ->when('extend', [Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookNotLentCannotBeExtendedException::class);
    }
}
