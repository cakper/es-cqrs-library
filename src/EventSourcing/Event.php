<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

interface Event
{
    public function getAggregateId() : UuidInterface;
}