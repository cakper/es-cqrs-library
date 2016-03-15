<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\AddBookCommand as DomainAddBookCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddBookConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('library:add-book')
            ->setDescription('Add Book to Library')
            ->addArgument(
                'title',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bookId = Uuid::uuid4();
        $command = new DomainAddBookCommand($bookId, $input->getArgument('title'));

        $this->getContainer()->get('command_bus')->handle($command);

        $output->writeln((string) $bookId);
    }
}