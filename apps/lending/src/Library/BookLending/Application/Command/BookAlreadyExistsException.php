<?php
declare(strict_types = 1);
namespace Library\BookLending\Application\Command;

use RuntimeException;

class BookAlreadyExistsException extends RuntimeException
{
}