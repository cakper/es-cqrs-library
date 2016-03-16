<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use Doctrine\ORM\Query;
use EventSourcing\DelegateMapper;
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
        $projector = $this->getContainer()->get('library.projections.available_books');
        $eventStore = $this->getContainer()->get('library.event_sourcing.event_store');

        $projector->flush();
        $eventClasses = DelegateMapper::findEvents($projector, 'handle');

        foreach ($eventStore->findEventsOfClasses($eventClasses) as $event) {
            try {
                DelegateMapper::call($projector, 'handle', $event);
            } catch (\Exception $e) {

            }
        }
    }
}