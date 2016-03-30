<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Cli;

use EventSourcing\Calendar;
use EventSourcing\Messaging\CommandBus;
use Library\BookLending\Application\Command\AddBookCommand;
use Library\BookLending\Application\Command\ExtendBookCommand;
use Library\BookLending\Application\Command\LendBookCommand;
use Library\BookLending\Application\Command\ReturnBookCommand;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookTestConsoleCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $commandBus;


    /**
     * BookTestConsoleCommand constructor.
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('library:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bookId = Uuid::uuid4();
        $readerId = Uuid::uuid4();

        $this->commandBus->handle(new AddBookCommand($bookId, 'Domain-Driven Design ' . rand(1, 1000)));

        $output->writeln((string)$bookId);

        $this->commandBus->handle(new LendBookCommand($bookId, $readerId, Calendar::getCurrentDateTime()->modify('+30 days')));
        $this->commandBus->handle(new ExtendBookCommand($bookId, Calendar::getCurrentDateTime()->modify('+60 days')));
        $this->commandBus->handle(new ReturnBookCommand($bookId));
        $this->commandBus->handle(new LendBookCommand($bookId, $readerId, Calendar::getCurrentDateTime()->modify('+30 days')));
        if (rand(0, 1)) {
            $this->commandBus->handle(new ReturnBookCommand($bookId));
        }
    }
}