<?php

namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\EntityManager;
use EventSourcing\ReadModel\Projection;

abstract class DoctrineProjection implements Projection
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * DoctrineProjection constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function flush()
    {
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            foreach ($this->getViewClasses() as $class) {
                $cmd = $this->entityManager->getClassMetadata($class);
                $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
                $connection->executeUpdate($q);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    abstract public function getViewClasses() : array;
}