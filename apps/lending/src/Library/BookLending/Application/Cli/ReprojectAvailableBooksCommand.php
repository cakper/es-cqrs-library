<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Cli;

use Doctrine\ORM\Query;
use EventSourcing\DelegateMapper\DelegateMapper;
use EventSourcing\EventStore\EventStore;
use Library\BookLending\Application\ReadModel\AvailableBooksProjection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReprojectAvailableBooksCommand extends Command
{
    /**
     * @var AvailableBooksProjection
     */
    private $availableBooksProjection;
    /**
     * @var EventStore
     */
    private $eventStore;


    /**
     * @param AvailableBooksProjection $availableBooksProjection
     * @param EventStore $eventStore
     */
    public function __construct(AvailableBooksProjection $availableBooksProjection, EventStore $eventStore)
    {
        $this->availableBooksProjection = $availableBooksProjection;
        $this->eventStore = $eventStore;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('library:reproject');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->availableBooksProjection->flush();
        $eventClasses = DelegateMapper::findEvents($this->availableBooksProjection, 'handle');

        $start = microtime(true);

//        foreach ($this->eventStore->findAllEvents() as $event) {
        foreach ($this->eventStore->findEventsOfClasses($eventClasses) as $event) {
            try {
                DelegateMapper::call($this->availableBooksProjection, 'handle', $event);
            } catch (\Exception $e) {

            }
        }
        $this->availableBooksProjection->persist();

        $stop = microtime(true) - $start;

        var_dump($stop);
    }
}