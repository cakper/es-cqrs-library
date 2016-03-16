<?php

namespace Infrastructure\Domain;

use BookLibrary\Domain\Book;
use BookLibrary\Domain\BookAddedEvent;
use BookLibrary\Domain\BookExtendedEvent;
use BookLibrary\Domain\BookLentEvent;
use BookLibrary\Domain\BookReturnedEvent;

class Type
{
    private static $aggregateMapping = [
        Book::class => 1
    ];

    private static $eventMapping = [
        BookAddedEvent::class => 1,
        BookExtendedEvent::class => 2,
        BookLentEvent::class => 3,
        BookReturnedEvent::class => 4,
    ];

    public static function forAggregate($aggregate)
    {
        return static::$aggregateMapping[get_class($aggregate)];
    }

    public static function forAggregateClass($aggregateClass)
    {
        return static::$aggregateMapping[$aggregateClass];
    }

    public static function forEvent($event)
    {
        return static::$eventMapping[get_class($event)];
    }

    public static function forEventClass($eventClass)
    {
        return static::$eventMapping[$eventClass];
    }

    public static function forEventClasses(array $eventClasses)
    {
        return array_map(function ($eventClass) {
            return static::forEventClass($eventClass);
        }, $eventClasses);
    }
}