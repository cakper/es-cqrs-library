<?php

namespace test\EventSourcing;

use EventSourcing\Messaging\Event;
use Exception;
use PHPUnit_Framework_TestCase;
use test\EventSourcing\Scenario;

abstract class ScenarioTest extends PHPUnit_Framework_TestCase
{
    protected abstract function getAggregateClass();

    /**
     * @var Scenario
     */
    private $scenario;

    protected function setUp()
    {
        FakeCalendar::fixReturnedValueOfNowCalls();
        $this->scenario = new Scenario($this->getAggregateClass(), $this);
    }

    /**
     * @param Event[] $events
     *
     * @return ScenarioTest
     */
    protected function given(array $events) : self
    {
        $this->scenario->given($events);
        return $this;
    }

    /**
     * @param $method
     * @param array $parameters
     *
     * @return ScenarioTest
     */
    protected function when($method, array $parameters = []) : self
    {
        $this->scenario->when($method, $parameters);
        return $this;
    }

    /**
     * @param Event[]|Exception $eventsOrException
     */
    protected function then($eventsOrException)
    {
        $this->scenario->then($eventsOrException);
    }
}
