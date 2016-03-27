<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use EventSourcing\Messaging\Command;
use Ramsey\Uuid\UuidInterface;

class AddBookCommand implements Command
{
    public $bookId;
    public $title;

    public function __construct(UuidInterface $bookId, string $title)
    {
        $this->bookId = $bookId;
        $this->title = $title;
    }
}