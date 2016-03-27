<?php

namespace Infrastructure\Domain;

use Library\Domain\Book;
use Library\Domain\BookAddedEvent;
use Library\Domain\BookExtendedEvent;
use Library\Domain\BookLentEvent;
use Library\Domain\BookReturnedEvent;

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

    public static function forEvent($event)
    {
        return static::forEventClass(get_class($event));
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