<?php

namespace test\Library\BookLending\Domain;

use EventSourcing\Calendar;
use Library\BookLending\Domain\Book;
use Library\BookLending\Domain\BookAddedEvent;
use Library\BookLending\Domain\BookAlreadyLentException;
use Library\BookLending\Domain\BookExtendedEvent;
use Library\BookLending\Domain\BookLentEvent;
use Library\BookLending\Domain\BookNotLentCannotBeExtendedException;
use Library\BookLending\Domain\BookNotLentCannotBeReturnedException;
use Library\BookLending\Domain\BookReturnedEvent;
use Ramsey\Uuid\Uuid;
use test\EventSourcing\ScenarioTest;

class BookTest extends ScenarioTest
{
    const TITLE = 'Domain-Driven Design';

    protected function getAggregateClass()
    {
        return Book::class;
    }

    public function testAddNewBookCopy()
    {
        $bookCopyId = Uuid::uuid4();

        $this
            ->when('add', [$bookCopyId, self::TITLE])
            ->then([new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE)]);
    }

    public function testLendingBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();
        $returnDueDate = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE)])
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('lendTo', [$readerId, $dueOn])
            ->then(BookAlreadyLentException::class);
    }

    public function testReturnBook()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('return')
            ->then([new BookReturnedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)]);
    }

    public function testReturnBookThatWasAlreadyReturned()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $dueOn = Calendar::getCurrentDateTime()->modify('+30 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn),
                new BookReturnedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)
            ])
            ->when('return')
            ->then(BookNotLentCannotBeReturnedException::class);
    }

    public function testReturnBookLate()
    {
        $readerId = Uuid::uuid4();
        $bookCopyId = Uuid::uuid4();

        $lentOn = Calendar::getCurrentDateTime()->modify('-32 days');
        $dueOn = Calendar::getCurrentDateTime()->modify('-2 days');

        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
            ])
            ->when('return')
            ->then([new BookReturnedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $dueOn)]);
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn)
            ])
            ->when('extend', [$newDue])
            ->then([new BookExtendedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $newDue)]);
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
                new BookAddedEvent(Calendar::getCurrentDateTime(), $bookCopyId, self::TITLE),
                new BookLentEvent($bookCopyId, $readerId, $lentOn, $dueOn),
                new BookExtendedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $newDue)
            ])
            ->when('return')
            ->then([new BookReturnedEvent($bookCopyId, $readerId, Calendar::getCurrentDateTime(), $newDue)]);
    }

    public function testExtendBookThatWasNotLent()
    {
        $this
            ->given([
                new BookAddedEvent(Calendar::getCurrentDateTime(), Uuid::uuid4(), self::TITLE)
            ])
            ->when('extend', [Calendar::getCurrentDateTime()->modify('+30 days')])
            ->then(BookNotLentCannotBeExtendedException::class);
    }
}
