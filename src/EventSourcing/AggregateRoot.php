<?php
declare(strict_types = 1);
namespace EventSourcing;

class AggregateRoot
{
    private $changes = [];
    private $version = 0;

    protected function __construct()
    {
    }

    public static function loadFromHistory(array $events)
    {
        /** @var self $aggregate */
        $aggregate = new static;

        foreach ($events as $event) {
            $aggregate->apply($event, false);
        }

        return $aggregate;
    }

    protected function apply(Event $event, $add = true)
    {
        DelegateMapper::call($this, 'apply', $event);

        if ($add) {
            $this->changes[] = $event;
        }

        $this->version++;
    }

    /**
     * Event[]
     */
    public function getChanges() : array
    {
        return $this->changes;
    }

    public function markChangesAsCommitted()
    {
        $this->changes = [];
    }

    public function hasChanges(): bool
    {
        return count($this->changes) > 0;
    }

    public function getVersion() : int
    {
        return $this->version;
    }

    public function getInitialVersion() : int
    {
        return $this->version - count($this->changes);
    }
}