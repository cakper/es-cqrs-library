<?php
declare(strict_types = 1);

namespace Library\BookLending\Application\Command;

use EventSourcing\Messaging\Command;
use Ramsey\Uuid\UuidInterface;

class ReturnBookCommand implements Command
{
    public $bookId;

    public function __construct(UuidInterface $bookId)
    {
        $this->bookId = $bookId;
    }
}