<?php

namespace EventSourcing\Repository;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Intl\Exception\RuntimeException;

class AggregateNotFoundException extends RuntimeException
{
    /**
     * @var UuidInterface
     */
    private $aggregateId;

    function __construct(UuidInterface $aggregateId)
    {
        $this->aggregateId = $aggregateId;
    }

    /**
     * @return UuidInterface
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }
}