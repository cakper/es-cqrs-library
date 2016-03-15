<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use Doctrine\ORM\Tools\Pagination\Paginator;
use EventSourcing\DelegateMapper;
use Infrastructure\EventStore\Doctrine\BookAvailableView;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReprojectAvailableBooksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('library:reproject');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $cmd = $em->getClassMetadata(BookAvailableView::class);
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $query = $em->createQuery("SELECT e FROM EventStore:Event e");

        $projector = $this->getContainer()->get('library.projections.available_books');

        foreach ($query->iterate() as $row) {

            $event = $row[0];
            try {
                DelegateMapper::call($projector, 'handle', $event->getDomainEvent());
                $em->detach($event);
            } catch (\Exception $e) {

            }
            $em->detach($row[0]);
        }
    }
}