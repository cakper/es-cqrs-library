<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\AddBookCommand as DomainAddBookCommand;
use BookLibrary\Application\LendBookCommand;
use EventSourcing\Calendar;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LendBookConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('library:lend-book')
            ->setDescription('Lent book to reader')
            ->addArgument(
                'book-id',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'reader-id',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new LendBookCommand(Uuid::fromString($input->getArgument('book-id')), Uuid::fromString($input->getArgument('reader-id')), Calendar::getCurrentDateTime()->modify('+30 days'));

        $this->getContainer()->get('command_bus')->handle($command);
    }
}