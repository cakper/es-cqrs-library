<?php
declare(strict_types = 1);
namespace EventSourcing;

use LogicException;
use ReflectionClass;
use ReflectionMethod;

class DelegateMapper
{
    private static $mappings = [];

    /**
     * @param $aggregate
     * @param $methodPrefix
     * @param $event
     *
     * @throws LogicException
     */
    public static function call($aggregate, $methodPrefix, $event)
    {
        $aggregateClass = get_class($aggregate);
        $eventClass = get_class($event);

        if (!array_key_exists($aggregateClass, self::$mappings)) {

            $mapping = [];

            $prefixLength = strlen($methodPrefix);
            $aggregateReflectionClass = new ReflectionClass($aggregateClass);

            foreach ($aggregateReflectionClass->getMethods() as $method) {
                if (substr($method->getName(), 0, $prefixLength) !== $methodPrefix) {
                    continue;
                }
                $reflectionParameters = $aggregateReflectionClass->getMethod($method->getName())->getParameters();
                if (count($reflectionParameters) === 1) {
                    $reflectionMethodParameterClass = $reflectionParameters[0]->getClass();
                    if (null !== $reflectionMethodParameterClass) {
                        if (is_a($reflectionMethodParameterClass->name, Event::class, true)) {
                            if (array_key_exists($reflectionMethodParameterClass->name, $mapping)) {
                                throw new DelegateMapperException(sprintf('Found two method capable of being called with "%s"', $reflectionMethodParameterClass->name));
                            }
                            $mapping[$reflectionMethodParameterClass->name] = $method->getName();
                        }
                    }
                }
            }

            self::$mappings[$aggregateClass] = $mapping;
        }

        if (!array_key_exists($eventClass, self::$mappings[$aggregateClass])) {
            throw new DelegateMapperException(sprintf('"%s*" delegate not found for event "%s"', $methodPrefix, get_class($event)));
        }

        $reflectionMethod = new ReflectionMethod($aggregate, self::$mappings[$aggregateClass][$eventClass]);
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($aggregate, $event);
    }
}