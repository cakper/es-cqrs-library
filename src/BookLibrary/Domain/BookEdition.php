<?php
declare(strict_types = 1);

namespace BookLibrary\Domain;

use EventSourcing\AggregateRoot;

class BookEdition extends AggregateRoot
{
    protected function applyIssued(BookEditionIssuedEvent $event)
    {

    }

    public static function issue(Isbn10 $isbn10)
    {
        $edition = new self;

        $edition->apply(new BookEditionIssuedEvent(Calendar::getCurrentDateTime(), $isbn10));

        return $edition;
    }
}