<?php
declare(strict_types = 1);
namespace EventSourcing;

class AggregateVersion
{
    /**
     * @var int
     */
    private $version;

    /**
     * AggregateVersion constructor.
     *
     * @param int $version
     */
    public function __construct(int $version)
    {
        if ($version < 0) {
            throw new InvalidAggregateVersionException();
        }

        $this->version = $version;
    }

    /**
     * @return int
     */
    public function toInt() : int
    {
        return $this->version;
    }
}