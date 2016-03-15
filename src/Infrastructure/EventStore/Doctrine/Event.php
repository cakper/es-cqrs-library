<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use EventSourcing\Event as DomainEvent;
use Ramsey\Uuid\UuidInterface;

/**
 * @Entity()
 * @Table(
 * name="event",
 * uniqueConstraints={@UniqueConstraint(name="aggregate_version_idx", columns={"aggregate_id", "version"})},
 * indexes={@Index(name="aggregate_id_idx", columns={"aggregate_id"})}
 * )
 */
class Event
{
    /**
     * @Id()
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;
    /**
     * @Column(type="guid", name="aggregate_id", nullable=false)
     */
    public $aggregateId;
    /**
     * @Column(type="integer", nullable=false)
     */
    public $version;
    /**
     * @Column(type="text", nullable=false)
     */
    public $data;

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