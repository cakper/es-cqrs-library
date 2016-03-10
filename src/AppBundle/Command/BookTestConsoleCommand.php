<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\AddBookCommand;
use BookLibrary\Application\ExtendBookCommand;
use BookLibrary\Application\LendBookCommand;
use BookLibrary\Application\ReturnBookCommand;
use EventSourcing\Calendar;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookTestConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('library:test')
            ->setDescription('Test flow');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandBus = $this->getContainer()->get('tactician.commandbus.default');

        $bookId = Uuid::uuid4();
        $readerId = Uuid::uuid4();

        $commandBus->handle(new AddBookCommand($bookId, 'Domain-Driven Design'));

        $output->writeln((string)$bookId);

        $commandBus->handle(new LendBookCommand($bookId, $readerId, Calendar::getCurrentDateTime()->modify('+30 days')));
        $commandBus->handle(new ExtendBookCommand($bookId, Calendar::getCurrentDateTime()->modify('+60 days')));
        $commandBus->handle(new ReturnBookCommand($bookId));
        $commandBus->handle(new LendBookCommand($bookId, $readerId, Calendar::getCurrentDateTime()->modify('+30 days')));
        if (rand(0, 1)) {
            $commandBus->handle(new ReturnBookCommand($bookId));
        }
    }
}