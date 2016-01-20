<?php
declare(strict_types = 1);

namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Event;

class BookEditionIssuedEvent implements Event
{
    /**
     * @var Isbn10
     */
    private $isbn10;
    /**
     * @var DateTimeImmutable
     */
    private $issuedOn;

    /**
     * BookIssuedEvent constructor.
     *
     * @param DateTimeImmutable $issuedOn
     * @param Isbn10            $isbn10
     */
    public function __construct(DateTimeImmutable $issuedOn, Isbn10 $isbn10)
    {
        $this->isbn10 = $isbn10;
        $this->issuedOn = $issuedOn;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getIssuedOn()
    {
        return $this->issuedOn;
    }

    public function getAggregateId() : AggregateId
    {
        return $this->isbn10;
    }
}