<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\ExtendBookCommand;
use EventSourcing\Calendar;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtendBookConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('library:extend-book')
            ->setDescription('Extend')
            ->addArgument(
                'book-id',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bookId = Uuid::fromString($input->getArgument('book-id'));
        $command = new ExtendBookCommand($bookId, Calendar::getCurrentDateTime()->modify('+60 days'));

        $this->getContainer()->get('tactician.commandbus.default')->handle($command);
    }
}