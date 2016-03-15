<?php
declare(strict_types = 1);
namespace EventSourcing;

use Countable;

class EventStream implements Countable
{
    private $events;

    /**
     * Events constructor.
     *
     * @param array $events
     */
    public function __construct(array $events)
    {
        foreach ($events as $event) {
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
     * @return EventStream
     */
    public function filter(callable $callback) : EventStream
    {
        return new static(array_filter($this->events), $callback);
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function map(callable $callback) : array
    {
        return array_map($callback, $this->events);
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->events);
    }
}