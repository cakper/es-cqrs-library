<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use LogicException;

class BookNotLentCannotBeExtendedException extends LogicException
{

}