<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use LogicException;

class BookCopyNotLentCannotBeExtendedException extends LogicException
{

}