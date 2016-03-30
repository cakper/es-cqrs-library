<?php

namespace EventSourcing\Test;

use ArrayIterator;
use EventSourcing\AggregateRoot;
use EventSourcing\DelegateMapper\DelegateMapperException;
use EventSourcing\Messaging\Event;
use Exception;
use PHPUnit_Framework_Assert;

class Scenario
{
    /**
     * @var PHPUnit_Framework_Assert
     */
    private $assert;

    /**
     * @var string Aggregate class name
     */
    private $aggregateClass;

    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var string
     */
    private $method;

    /**
     * @var AggregateRoot
     */
    private $aggregateRoot;

    public function __construct($aggregateClass, PHPUnit_Framework_Assert $assert)
    {
        $this->assert = $assert;
        $this->aggregateClass = $aggregateClass;
    }

    /**
     * @param array $events
     *
     * @return self
     */
    public function given(array $events = null)
    {
        $this->aggregateRoot = call_user_func([$this->aggregateClass, 'loadFromHistory'], new ArrayIterator($events));

        return $this;
    }

    /**
     * @param       $method
     * @param array $parameters
     *
     * @return self
     */
    public function when($method, array $parameters = [])
    {
        if (!method_exists($this->aggregateClass, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" does not exist on class "%s"', $method, $this->aggregateClass));
        }

        $this->method = $method;

        try {
            if (is_a($this->aggregateRoot, $this->aggregateClass, true)) {
                call_user_func_array([$this->aggregateRoot, $method], $parameters);
            } else {
                $this->aggregateRoot = call_user_func_array([$this->aggregateClass, $method], $parameters);
            }
        } catch (DelegateMapperException $e) {
            throw $e;
        } catch (Exception $exception) {
            $this->exception = $exception;
        }

        return $this;
    }

    /**
     * @param Event[]|Exception $eventsOrException
     *
     * @return Scenario
     */
    public function then($eventsOrException)
    {
        if (is_a($eventsOrException, Exception::class, true)) {
            if (is_null($this->exception)) {
                throw new \LogicException(sprintf('Expected "%s" to be thrown from "%s::%s"', $eventsOrException, $this->aggregateClass, $this->method));
            }

            if (!is_a($this->exception, $eventsOrException, true)) {
                throw new \LogicException(sprintf('Expected "%s" to be thrown from "%s::%s", got "%s"', $eventsOrException, $this->aggregateClass, $this->method, get_class($this->exception)));
            }
        } elseif ($this->exception instanceof Exception) {
            throw new \LogicException(sprintf('Expected events but got Exception "%s"', get_class($this->exception)));
        } elseif (is_array($eventsOrException)) {
            $this->assert->assertEquals(new ArrayIterator($eventsOrException), $this->aggregateRoot->getChanges());
        } else {
            throw new \LogicException('Unexpected expectation');
        }
    }
}