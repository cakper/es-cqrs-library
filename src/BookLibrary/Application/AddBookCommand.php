<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use Ramsey\Uuid\UuidInterface;

class AddBookCommand
{
    public $bookId;
    public $title;

    public function __construct(UuidInterface $bookId, string $title)
    {
        $this->bookId = $bookId;
        $this->title = $title;
    }
}