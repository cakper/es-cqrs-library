<?php
declare(strict_types = 1);
namespace Library\Domain;

use LogicException;

class BookNotLentCannotBeExtendedException extends LogicException
{

}