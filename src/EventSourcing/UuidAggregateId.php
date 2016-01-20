<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidAggregateId implements AggregateId
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * BookId constructor.
     *
     * @param UuidInterface $uuid
     */
    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public static function generate()
    {
        return new static(Uuid::uuid4());
    }
}