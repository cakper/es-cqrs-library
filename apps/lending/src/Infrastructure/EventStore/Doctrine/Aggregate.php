<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\UuidInterface;

/**
 * @Entity()
 * @Table(name="aggregate")
 */
class Aggregate
{
    /**
     * @Id
     * @Column(type="guid", name="aggregate_id", nullable=false)
     */
    public $aggregateId;

    /**
     * @Column(type="integer", nullable=false)
     */
    public $type;

    /**
     * @Column(type="integer", nullable=false)
     */
    public $version;

    public function __construct(UuidInterface $aggregateId, int $type, int $version = 0)
    {
        $this->aggregateId = $aggregateId;
        $this->type = $type;
        $this->version = $version;
    }
}