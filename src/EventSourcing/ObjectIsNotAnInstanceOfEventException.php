<?php
declare(strict_types = 1);
namespace EventSourcing;

use InvalidArgumentException;

class ObjectIsNotAnInstanceOfEventException extends InvalidArgumentException
{

}