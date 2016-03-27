<?php

namespace EventSourcing\EventStore;

interface TypeMapping
{
    public function forAggregateClass($class);

    public static function forEventClass($class);

    public static function forEventClasses(array $classes);
}