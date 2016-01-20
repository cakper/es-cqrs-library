<?php
declare(strict_types = 1);
namespace EventSourcing;

interface Event
{
    public function getAggregateId() : AggregateId;
}