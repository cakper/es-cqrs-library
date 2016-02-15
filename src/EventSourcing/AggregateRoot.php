<?php
declare(strict_types = 1);
namespace EventSourcing;

use Ramsey\Uuid\UuidInterface;

abstract class AggregateRoot
{
    const VERSION_NEW = 0;

    private $changes = [];
    private $version = self::VERSION_NEW;

    protected function __construct()
    {
    }

    abstract public function getAggregateId() : UuidInterface;
    abstract public static function getType() : string;

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

    public function getOriginatingVersion() : int
    {
        return $this->version - count($this->changes);
    }
}