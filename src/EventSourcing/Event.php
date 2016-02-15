<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

interface Event
{
    public function getAggregateId() : UuidInterface;
    public function toArray() : array;
    public static function fromArray(array $data): Event;
}