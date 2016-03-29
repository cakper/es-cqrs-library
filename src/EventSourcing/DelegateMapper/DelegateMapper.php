<?php
declare(strict_types = 1);
namespace EventSourcing\DelegateMapper;

use EventSourcing\Messaging\Event;
use ReflectionClass;
use ReflectionMethod;

class DelegateMapper
{
    private static $mappings = [];

    /**
     * @param $object
     * @param $methodPrefix
     * @param $event
     *
     * @throws DelegateMapperException
     */
    public static function call($object, $methodPrefix, $event)
    {
        $objectClass = get_class($object);
        $eventClass = get_class($event);

        if (!array_key_exists($objectClass, self::$mappings)) {
            self::$mappings[$objectClass] = self::findMappingFor($objectClass, $methodPrefix);
        }

        if (!array_key_exists($eventClass, self::$mappings[$objectClass])) {
            throw new DelegateMapperException(sprintf('"%s*" delegate not found for event "%s"', $methodPrefix, get_class($event)));
        }

        $reflectionMethod = new ReflectionMethod($object, self::$mappings[$objectClass][$eventClass]);
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($object, $event);
    }

    /**
     * @param $objectClass
     * @param $methodPrefix
     * @return array
     */
    private static function findMappingFor($objectClass, $methodPrefix)
    {
        $mapping = [];

        $prefixLength = strlen($methodPrefix);
        $objectReflectionClass = new ReflectionClass($objectClass);

        foreach ($objectReflectionClass->getMethods() as $method) {
            if (substr($method->getName(), 0, $prefixLength) !== $methodPrefix) {
                continue;
            }
            $reflectionParameters = $objectReflectionClass->getMethod($method->getName())->getParameters();
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
        return $mapping;
    }

    public static function findEvents($object, $methodPrefix)
    {
        $objectClass = get_class($object);

        if (!array_key_exists($objectClass, self::$mappings)) {
            self::$mappings[$objectClass] = self::findMappingFor($objectClass, $methodPrefix);
        }

        return array_keys(self::$mappings[$objectClass]);
    }
}