<?php
declare(strict_types = 1);
namespace Application;

use EventStore\EventStore;
use Pimple\Container as PimpleContainer;

class Container
{
    private static $instance;

    public static function get()
    {
        if (!self::$instance instanceof PimpleContainer) {
            self::$instance = self::buildContainer();
        }

        return self::$instance;
    }

    private static function buildContainer()
    {
        $container = new PimpleContainer();

        $container['get_event_store_url'] = 'http://127.0.0.1:2113';

        $container['get_event_store'] = function ($c) {
            return new EventStore($c['event_store_url']);
        };

        return $container;
    }
}