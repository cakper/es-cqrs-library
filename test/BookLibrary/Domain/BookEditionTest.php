<?php

namespace test\BookLibrary\Domain;

use BookLibrary\Domain\BookEdition;
use BookLibrary\Domain\BookEditionIssuedEvent;
use BookLibrary\Domain\Calendar;
use BookLibrary\Domain\Isbn10;
use Ramsey\Uuid\Uuid;

class BookEditionTest extends ScenarioTest
{
    protected function getAggregateClass()
    {
        return BookEdition::class;
    }

    public function testIssueNewBook()
    {
        $isbn10 = new Isbn10('abc');

        $this->when('issue', [$isbn10])
            ->then([new BookEditionIssuedEvent(Calendar::getCurrentDateTime(), $isbn10)]);
    }

    public function testAddingNewBookCopy()
    {
        $isbn10 = new Isbn10('abc');
        $this
            ->given([new BookEditionIssuedEvent(Calendar::getCurrentDateTime(), $isbn10)])
            ->when('addNewBookCopy', [Uuid::uuid4()])
            ->then([]);
    }
}