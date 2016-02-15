<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use EventSourcing\Event as DomainEvent;
use Ramsey\Uuid\UuidInterface;

/**
 * @Entity()
 * @Table(name="event", uniqueConstraints={})
 */
class Event
{
    /**
     * @Id
     * @Column(type="string", length=36, name="aggregate_id", nullable=false)
     */
    private $aggregateId;
    /**
     * @Id
     * @Column(type="integer", nullable=false)
     */
    private $version;
    /**
     * @Column(type="text", nullable=false)
     */
    private $data;

    public function __construct(UuidInterface $aggregateId, int $version, DomainEvent $event)
    {
        $this->aggregateId = $aggregateId;
        $this->version = $version;
        $this->data = json_encode(['type' => get_class($event), 'data' => $event->toArray()]);
    }

    public function getDomainEvent() : DomainEvent
    {
        $data = json_decode($this->data, true);

        return call_user_func([$data['type'], 'fromArray'], $data['data']);
    }
}