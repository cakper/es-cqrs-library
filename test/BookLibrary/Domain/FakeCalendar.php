<?php
declare(strict_types = 1);
namespace test\BookLibrary\Domain;

use BookLibrary\Calendar;
use DateTimeImmutable;
use ReflectionClass;

class FakeCalendar extends Calendar
{
    private $now;

    public function currentDateTime() : DateTimeImmutable
    {
        if (is_null($this->now)) {
            $this->now = new DateTimeImmutable();
        }

        return $this->now;
    }

    public static function fixReturnedValueOfNowCalls()
    {
        $fakeInstance = static::instance();

        $class = new ReflectionClass(Calendar::class);
        $property = $class->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue($fakeInstance);
    }
}