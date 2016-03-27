<?php

namespace Library\BookLending\Infrastructure\EventStore;

use EventSourcing\EventStore\TypeMapping;
use Library\BookLending\Domain\Book;
use Library\BookLending\Domain\BookAddedEvent;
use Library\BookLending\Domain\BookExtendedEvent;
use Library\BookLending\Domain\BookLentEvent;
use Library\BookLending\Domain\BookReturnedEvent;

class BookLendingTypeMapping implements TypeMapping
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

    public function forAggregateClass($class)
    {
        return static::$aggregateMapping[$class];
    }

    public static function forEventClass($class)
    {
        return static::$eventMapping[$class];
    }

    public static function forEventClasses(array $classes)
    {
        return array_map(function ($class) {
            return static::forEventClass($class);
        }, $classes);
    }
}