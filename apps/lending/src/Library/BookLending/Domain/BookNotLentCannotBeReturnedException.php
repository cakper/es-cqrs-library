<?php
declare(strict_types = 1);
namespace Library\BookLending\Domain;

use LogicException;

class BookNotLentCannotBeReturnedException extends LogicException
{

}