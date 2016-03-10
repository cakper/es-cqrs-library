<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use Doctrine\ORM\Tools\Pagination\Paginator;
use EventSourcing\DelegateMapper;
use EventSourcing\Event as DomainEvent;
use EventSourcing\Events;
use Infrastructure\EventStore\Doctrine\BookAvailableView;
use Infrastructure\EventStore\Doctrine\Event;
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
            $output->writeln('Projection cleared');
        } catch (\Exception $e) {
            $connection->rollback();
        }

//        $eventRepository = $em->getRepository(Event::class);
//        $events = $eventRepository->findAll();

        $paginator = new Paginator($em->createQuery("SELECT e FROM EventStore:Event e")
            ->setFirstResult(0)
            ->setMaxResults(100));

//        $c = count($paginator);

//        $output->writeln(sprintf('Reprojecting %s events', count($events)));
        $projector = $this->getContainer()->get('library.projections.available_books');

        $counter = 0;

        foreach ($paginator as $event) {

            try {
                $counter++;
                DelegateMapper::call($projector, 'handle', $event->getDomainEvent());
                $em->detach($event);
                echo $counter.PHP_EOL;
            } catch (\Exception $e) {

            }
        }

//        $events = new Events(array_map(function (Event $event) : DomainEvent {
//            return $event->getDomainEvent();
//        }, $events));

//
//        $events->each(function (DomainEvent $domainEvent) use ($projector) {
//            try {
//                DelegateMapper::call($projector, 'handle', $domainEvent);
//            } catch (\Exception $e) {
//
//            }
//        });
    }
}