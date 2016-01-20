<?php
declare(strict_types = 1);

namespace BookLibrary\Domain;

use EventSourcing\AggregateId;

class Isbn10 implements AggregateId
{
    /**
     * @var
     */
    private $isbn10;

    /**
     * Isbn10 constructor.
     */
    public function __construct($isbn10)
    {
        $this->isbn10 = $isbn10;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->isbn10;
    }
}