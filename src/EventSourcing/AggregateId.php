<?php
declare(strict_types = 1);
namespace EventSourcing;

interface AggregateId
{
    /**
     * @return string
     */
    public function __toString(): string;
}