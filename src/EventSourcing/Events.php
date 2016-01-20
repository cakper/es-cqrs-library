<?php
declare(strict_types = 1);
namespace EventSourcing;

class Events
{
    private $events;

    /**
     * Events constructor.
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        foreach ($this->events as $event) {
            if (!$event instanceof Event) {
                throw new ObjectIsNotAnInstanceOfEventException();
            }
        }

        $this->events = $events;
    }

    /**
     * @param callable $callback
     */
    public function each(callable $callback)
    {
        array_walk($this->events, $callback);
    }

    /**
     * @param callable $callback
     *
     * @return Events
     */
    public function filter(callable $callback) : static
    {
        return new static(array_filter($this->events), $callback);
    }
}