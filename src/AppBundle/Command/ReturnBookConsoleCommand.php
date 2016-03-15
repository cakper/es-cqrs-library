<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\AddBookCommand as DomainAddBookCommand;
use BookLibrary\Application\LendBookCommand;
use BookLibrary\Application\ReturnBookCommand;
use EventSourcing\Calendar;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReturnBookConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('library:return-book')
            ->setDescription('Return book to library')
            ->addArgument(
                'book-id',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new ReturnBookCommand(Uuid::fromString($input->getArgument('book-id')));

        $this->getContainer()->get('command_bus')->handle($command);
    }
}