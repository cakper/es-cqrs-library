<?php
declare(strict_types = 1);
namespace EventSourcing;

use DateTimeImmutable;

class Calendar
{
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function currentDateTime() : DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public static function getCurrentDateTime()
    {
        return self::instance()->currentDateTime();
    }
}