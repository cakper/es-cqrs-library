<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Domain\BookAddedEvent;
use BookLibrary\Domain\BookLentEvent;
use BookLibrary\Domain\BookReturnedEvent;
use Doctrine\ORM\Query;
use EventSourcing\DelegateMapper;
use Infrastructure\Domain\Type;
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
        $serializer = $this->getContainer()->get('library.event_sourcing.serializer');
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

        $query = $em->createQuery("SELECT e FROM EventStore:Event e where e.type in (:types)");
        $query->setParameter('types', Type::forEventClasses([BookAddedEvent::class, BookLentEvent::class, BookReturnedEvent::class]));

        $projector = $this->getContainer()->get('library.projections.available_books');

        foreach ($query->iterate(null, Query::HYDRATE_ARRAY) as $row) {

            $event = $row[0];
            try {
                DelegateMapper::call($projector, 'handle', $serializer->deserialize($event['data'], $event['type'], 'json'));
                $em->detach($event);
            } catch (\Exception $e) {

            }
        }
    }
}