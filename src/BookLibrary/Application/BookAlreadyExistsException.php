<?php
declare(strict_types = 1);
namespace BookLibrary\Application;

use RuntimeException;

class BookAlreadyExistsException extends RuntimeException
{

    /**
     * BookAlreadyExistsException constructor.
     */
    public function __construct()
    {
    }
}